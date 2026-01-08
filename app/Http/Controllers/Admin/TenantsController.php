<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TenantProvisioner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TenantsController extends Controller
{
    public function index()
    {
        $tenants = \App\Models\Tenant::all();
        return response()->json($tenants);
    }

    public function showAdminView()
    {
        $tenants = \App\Models\Tenant::orderBy('created_at', 'desc')->get();
        return Inertia::render('SuperAdmin/Index', [
            'tenants' => $tenants
        ]);
    }

    public function store(Request $request, TenantProvisioner $provisioner)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'domain' => ['nullable', 'string', 'unique:tenants,domain'],
            'database' => ['nullable', 'string', 'unique:tenants,database'],
        ]);

        $name = trim($data['name']);
        $domain = $data['domain'] ?? null;
        if (!$domain) {
            $slug = Str::slug($name, '');
            $domain = $slug . '.app.test';
        }


        $tenant = \App\Models\Tenant::create([
            'name' => $name,
            'domain' => $domain,
            'database' => $data['database'] ?? null,
            'status' => 'provisioning',

        ]);


        try {
            $provisioner->provision($tenant);

            if ($request->expectsJson()) { // Para pruebas
                return response()->json($tenant->fresh(), 201);
            }
            return redirect()->back()->with('success', 'Tenant creado exitosamente. Base de datos provisionada.'); // Para Front

        } catch (\Throwable $e) {
            $tenant->status = 'failed';
            $tenant->save();

            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error al crear tenant: ' . $e->getMessage());
        }
    }
}
