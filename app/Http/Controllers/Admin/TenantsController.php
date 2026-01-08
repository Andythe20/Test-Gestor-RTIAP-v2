<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TenantsController extends Controller
{
    // Funcion para listar todos los tenants
    public function index()
    {
        $tenants = \App\Models\Tenant::all();
        return response()->json($tenants);
    }

    // Vista de administración para Inertia
    public function showAdminView()
    {
        $tenants = \App\Models\Tenant::orderBy('created_at', 'desc')->get();
        return Inertia::render('SuperAdmin/Index', [
            'tenants' => $tenants
        ]);
    }

    public function store(Request $request, \App\Services\TenantProvisioner $provisioner)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'domain' => ['required', 'string', 'unique:tenants,domain'],
        ]);

        $tenant = \App\Models\Tenant::create([
            'name' => $data['name'],
            'domain' => $data['domain'],
            'status' => 'provisioning',

        ]);

        try {
            $provisioner->provision($tenant);
            //return response()->json($tenant->fresh(), 201);
            return redirect()->back()->with('success', 'Tenant creado exitosamente. Base de datos provisionada.');
        } catch (\Throwable $e) {
            $tenant->status = 'failed';
            $tenant->save();
            //return response()->json(['message' => $e->getMessage()], 500);
            return redirect()->back()->with('error', 'Error al crear tenant: ' . $e->getMessage());
        }
    }
}
