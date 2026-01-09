<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\Empresa;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\Tenant;
use App\Models\Venta;
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
        $tenants = Tenant::query()->orderByDesc('created_at')->get();

        return Inertia::render('SuperAdmin/Index', [
            'tenants' => $tenants,
        ]);
    }

    public function show(Tenant $tenant)
    {
        try {
            $tenant->makeCurrent();
            $empresa = Empresa::query()->first();
            $sucursales = Sucursal::query()->get();
            $estadisticas = [
                'empleados' => Empleado::query()->count(),
                'productos' => Producto::query()->count(),
                'ventas' => Venta::query()->count(),
                'sucursales' => $sucursales->count(),
            ];

            return Inertia::render('SuperAdmin/TenantInfo', [
                'tenant' => $tenant,
                'empresa' => $empresa,
                'sucursales' => $sucursales,
                'estadisticas' => $estadisticas,
                'error' => null,
            ]);

        } catch (\Throwable $e) {
            $tenant->forceFill(['status' => 'failed'])->save();

            return Inertia::render('SuperAdmin/TenantInfo', [
                'tenant' => $tenant,
                'empresa' => null,
                'sucursales' => [],
                'estadisticas' => [],
                'error' => $e->getMessage(),
            ]);
        } finally {
            try {
                $tenant->forgetCurrent();
            } catch (\Throwable $ignored) {
            }
        }

    }

    public function store(Request $request, TenantProvisioner $provisioner)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'path' => ['nullable', 'string', 'unique:tenants,path'],
            'database' => ['nullable', 'string', 'unique:tenants,database'],
        ]);

        $name = trim($data['name']);
        $path = $data['path'] ?? Str::slug($name);

        $tenant = Tenant::create([
            'name' => $name,
            'path' => $path,
            'database' => $data['database'] ?? null,
            'status' => 'provisioning',

        ]);

        try {
            $provisioner->provision($tenant);

            if (! $tenant->password) {
                $password = 'hola123';
            }
            $email = $data['email'] ?? null;
            if (! is_string($email) || trim($email) === '') {
                $email = 'admin@'.$tenant->path.'.cl';
            }

            \App\Models\User::create([
                'name' => $tenant->name.'Admin',
                'email' => $email,
                'password' => $password,
                'role' => 'tenant',
                'tenant_id' => $tenant->id,
                'database' => $tenant->database,
            ]);

            if ($request->expectsJson()) { // Para pruebas
                return response()->json($tenant->fresh(), 201);
            }

            return redirect()->back()->with('success', 'Tenant creado exitosamente. Base de datos provisionada y credenciales de acceso generadas.'); // Para Front

        } catch (\Throwable $e) {
            $tenant->forceFill(['status' => 'failed'])->save();

            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Error al crear tenant: '.$e->getMessage());
        }
    }
}
