<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\Empresa;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Venta;
use App\Services\TenantProvisioner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            $decryptedPassword = $tenant->getDecryptedDbPassword();

            if (empty($tenant->database) || empty($tenant->db_username) || empty($decryptedPassword)) {
                throw new \RuntimeException('Tenant database credentials are missing or incomplete.');
            }
            $base = config('database.connections.tenant');

            config(['database.connections.tenant' => array_merge($base, [
                'database' => $tenant->database,
                'username' => $tenant->db_username,
                'password' => $decryptedPassword,
            ])]);

            DB::purge('tenant');
            DB::reconnect('tenant');

            $empresa = Empresa::on('tenant')->first();
            $sucursales = Sucursal::on('tenant')->get();
            $estadisticas = [
                'empleados' => Empleado::on('tenant')->count(),
                'productos' => Producto::on('tenant')->count(),
                'ventas' => Venta::on('tenant')->count(),
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
            logger()->error('Error rendering tenant info: '.$e->getMessage(), [
                'exception' => $e,
                'tenant_id' => $tenant->id,
            ]);

            $tenant->forceFill([
                'error_message' => $e->getMessage(),
            ])->save();

            return Inertia::render('SuperAdmin/TenantInfo', [
                'tenant' => $tenant,
                'empresa' => null,
                'sucursales' => [],
                'estadisticas' => [],
                'error' => $e->getMessage(),
            ]);
        } finally {
            try {
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

            $email = $data['email'] ?? null;
            $password = $data['password'] ?? 'hola123';

            if (! is_string($email) || trim($email) === '') {
                $email = 'admin@'.$tenant->path.'.cl';
            }
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Email inválido'], 422);
                }

                return redirect()->back()->with('error', 'Email inválido');
            }

            User::create([
                'name' => $tenant->name,
                'email' => $email,
                'password' => $password,
                'role' => 'tenant',
                'tenant_id' => $tenant->id,
                'database' => $tenant->database,
            ]);

            if ($request->expectsJson()) { // Para pruebas
                return response()->json($tenant->fresh(), 201);
            }

            /**
             * Redirigir a la página principal del administrador (GET /admin) en lugar de volver
             * a la URL de POST (/admin/tenants). Redirigir después de un POST puede provocar que
             * el navegador ejecute un GET en la ruta POST no definida (Método no permitido).
             * Use la ruta especificada para la página de inicio del administrador para que el cliente reciba un GET correcto.*/
            return redirect()->route('admin.home')
                ->with('success', 'Tenant creado exitosamente. Base de datos provisionada y credenciales de acceso generadas.'); // Para Front

        } catch (\Throwable $e) {
            $tenant->forceFill(['status' => 'failed'])->save();

            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 500);
            }

            return redirect()->route('admin.home')
                ->with('error', 'Error al crear tenant: '.$e->getMessage());
        }
    }
}
