<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\TenantProvisioner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TenantsController extends Controller
{
    public function index()
    {
        $tenants = Tenant::all();

        return response()->json($tenants);
    }

    public function showAdminView()
    {
        $tenants = Tenant::orderBy('created_at', 'desc')->get();

        return Inertia::render('SuperAdmin/Index', [
            'tenants' => $tenants,
        ]);
    }

    public function show(Tenant $tenant)
    {
        try {
            $tenant->makeCurrent();
        } catch (\Throwable $e) {
            $tenant->status = 'failed';
            $tenant->save();

            return Inertia::render('SuperAdmin/TenantInfo', [
                'tenant' => $tenant,
                'empresa' => null,
                'sucursales' => [],
                'estadisticas' => [],
                'error' => $e->getMessage(),
            ]);
        }

        $empresa = \App\Models\Empresa::first();
        $sucursales = \App\Models\Sucursal::all();
        $empleados = \App\Models\Empleado::count();
        $productos = \App\Models\Producto::count();
        $ventas = \App\Models\Venta::count();

        return Inertia::render('SuperAdmin/TenantInfo', [
            'tenant' => $tenant,
            'empresa' => $empresa,
            'sucursales' => $sucursales,
            'estadisticas' => [
                'empleados' => $empleados,
                'productos' => $productos,
                'ventas' => $ventas,
                'sucursales' => $sucursales->count(),
            ],
        ]);
    }

    public function store(Request $request, TenantProvisioner $provisioner)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'path' => ['nullable', 'string', 'unique:tenants,path'],
            'database' => ['nullable', 'string', 'unique:tenants,database'],
        ]);

        $name = trim($data['name']);
        $path = $data['path'] ?? null;
        if (!$path) {
            $slug = Str::slug($name, '');
            $domain = $slug.'';
        }

        $tenant = Tenant::create([
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

            return redirect()->back()->with('error', 'Error al crear tenant: '.$e->getMessage());
        }
    }
}
