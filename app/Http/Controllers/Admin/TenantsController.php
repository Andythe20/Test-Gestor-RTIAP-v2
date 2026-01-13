<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\Empresa;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\Tarjeta;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Venta;
use App\Services\TenantProvisioner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
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

        /**
         * Si después de crear un tenant se mostró un token de API en la sesión,
         * se pasa de forma explícita como una prop para que el cliente pueda leerlo
         */
        $apiToken = session('api_token');

        return Inertia::render('SuperAdmin/Index', [
            'tenants' => $tenants,
            'api_token' => $apiToken,
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

            $tenant->makeCurrent();
            $empresa = Empresa::on('tenant')->first();
            $sucursales = Sucursal::on('tenant')->get();
            // New: fetch core tenant data entities to display in the admin view
            $empleadosList = Empleado::query()->orderByDesc('created_at')->limit(10)->get();
            $productosList = Producto::query()->orderByDesc('created_at')->limit(10)->get();
            $tarjetasList = Tarjeta::query()->orderByDesc('created_at')->limit(10)->get();
            $ventasList = Venta::query()
                ->with(['empleado', 'producto', 'tarjeta'])
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();

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
                'empleados' => $empleadosList,
                'productos' => $productosList,
                'tarjetas' => $tarjetasList,
                'ventas' => $ventasList,
                'estadisticas' => $estadisticas,
                'error' => null,
            ]);
        } catch (\Throwable $e) {
            logger()->error('Error rendering tenant info: ' . $e->getMessage(), [
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
                'empleados' => [],
                'productos' => [],
                'tarjetas' => [],
                'ventas' => [],
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

            // Crea las credenciales del tenant
            $email = $data['email'] ?? null;
            $password = $data['password'] ?? 'hola123';

            if (! is_string($email) || trim($email) === '') {
                $email = 'admin@' . $tenant->path . '.cl';
            }
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Email inválido'], 422);
                }

                return redirect()->back()->with('error', 'Email inválido');
            }

            $user = User::create([
                'name' => $tenant->name,
                'email' => $email,
                'password' => $password,
                'role' => 'tenant',
                'tenant_id' => $tenant->id,
                'database' => $tenant->database,
            ]);

            // Crea el Token del tenant y lo muestra 1 sola vez.
            $token = $tenant->generateApiToken();

            if ($request->expectsJson()) {
                return response()->json(['tenant' => $tenant->fresh(), 'api_token' => $token, 'user' => $user], 201);
            }

            return redirect()->route('admin.home')
                ->with('success', 'Tenant creado exitosamente. Base de datos provisionada, credenciales y token generados.')
                ->with('api_token', $token);
        } catch (\Throwable $e) {
            $tenant->forceFill(['status' => 'failed'])->save();

            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 500);
            }

            return redirect()->route('admin.home')
                ->with('error', 'Error al crear tenant: ' . $e->getMessage());
        }
    }

    public function users(Tenant $tenant)
    {
        $users = User::on('landlord')
            ->select('id', 'name', 'email', 'tenant_id', 'is_admin', 'created_at')
            ->where('tenant_id', $tenant->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'tenant_id' => $tenant->id,
            'users' => $users,
        ]);
    }

    public function seed(Tenant $tenant)
    {
        try {
            $decryptedPassword = $tenant->getDecryptedDbPassword();
            $candidatePassword = $decryptedPassword ?: env('TENANT_DEMO_PASSWORD', 'Tenant.1234');

            if (empty($tenant->database) || empty($tenant->db_username) || empty($candidatePassword)) {
                return redirect()->route('admin.tenants.show', $tenant->id)
                    ->with('error', 'Credenciales de BD del tenant incompletas.');
            }

            config(['database.connections.tenant' => [
                'driver' => 'mysql',
                'host' => env('TENANT_DB_HOST', env('DB_HOST', '127.0.0.1')),
                'port' => env('TENANT_DB_PORT', env('DB_PORT', '3306')),
                'database' => $tenant->database,
                'username' => $tenant->db_username,
                'password' => $candidatePassword,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'strict' => true,
            ]]);

            DB::purge('tenant');
            DB::reconnect('tenant');

            DB::connection('tenant')->select('SELECT 1');

            $tenant->makeCurrent();

            // If we connected using the fallback demo password because the
            // encrypted one was missing, persist it now for future ops.
            if (empty($decryptedPassword)) {
                $tenant->forceFill([
                    'db_password_encrypted' => encrypt($candidatePassword),
                    'error_message' => null,
                ])->save();
            }

            // Run tenant demo seeder
            Artisan::call('db:seed', [
                '--database' => 'tenant',
                '--class' => \Database\Seeders\TenantDatabaseSeeder::class,
                '--force' => true,
            ]);

            return redirect()->route('admin.tenants.show', $tenant->id)
                ->with('success', 'Datos de ejemplo insertados en el tenant.');
        } catch (\Throwable $e) {
            logger()->error('Error seeding tenant demo data: ' . $e->getMessage(), [
                'exception' => $e,
                'tenant_id' => $tenant->id,
            ]);

            $tenant->forceFill(['error_message' => $e->getMessage()])->save();

            return redirect()->route('admin.tenants.show', $tenant->id)
                ->with('error', 'Error al insertar datos de ejemplo: ' . $e->getMessage());
        } finally {
            try {
                $tenant->forgetCurrent();
            } catch (\Throwable $ignored) {
            }
        }
    }
}
