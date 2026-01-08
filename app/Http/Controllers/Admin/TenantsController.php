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
        $tenant->makeCurrent();

        try {
            $tenantPassword = null;
            if (! empty($tenant->db_password_encrypted)) {
                $tenantPassword = decrypt($tenant->db_password_encrypted);
            }

            config(['database.connections.tenant' => [
                'driver' => 'mysql',
                'host' => env('TENANT_DB_HOST', env('DB_HOST', '127.0.0.1')),
                'port' => env('TENANT_DB_PORT', env('DB_PORT', 3306)),
                'database' => $tenant->database,
                'username' => $tenant->db_username ?? env('DB_USERNAME'),
                'password' => $tenantPassword ?? env('DB_PASSWORD'),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'strict' => true,
            ]]);

            \Illuminate\Support\Facades\DB::purge('tenant');
            \Illuminate\Support\Facades\DB::reconnect('tenant');
        } catch (\Throwable $e) {
            // If configuring tenant DB fails we'll mark tenant as failed and show an error page
            $tenant->status = 'failed';
            $tenant->save();

            return Inertia::render('SuperAdmin/TenantInfo', [
                'tenant' => $tenant,
                'empresa' => null,
                'sucursales' => [],
                'estadisticas' => [
                    'empleados' => 0,
                    'productos' => 0,
                    'ventas' => 0,
                    'sucursales' => 0,
                ],
                'error' => 'Error connecting to tenant database: '.$e->getMessage(),
            ]);
        }

        // Obtener datos del tenant desde su base de datos
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
    // Funcion para ver un tenant específico al hacer clic en él
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'domain' => ['nullable', 'string', 'unique:tenants,domain'],
            'database' => ['nullable', 'string', 'unique:tenants,database'],
        ]);

        $name = trim($data['name']);
        $domain = $data['domain'] ?? null;
        if (! $domain) {
            $slug = Str::slug($name, '');
            $domain = $slug.'.app.test';
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
