<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TenantContext
{
    public function handle(Request $request, Closure $next)
    {
        $param = $request->route('tenant');

        // Si no viene nada, 404
        if (! $param) {
            abort(404);
        }

        // Si ya viene como modelo (binding OK), úsalo.
        // Si viene como string (binding NO corrió), búscalo por path.
        $tenant = $param instanceof Tenant
            ? $param
            : Tenant::on('landlord')->where('path', (string) $param)->firstOrFail();

        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        // Admin opcional: deja pasar
        if (! $user->is_admin && (int) $user->tenant_id !== (int) $tenant->id) {
            abort(403);
        }

        // Configurar la conexión "tenant" en runtime usando las credenciales almacenadas
        $decryptedPassword = $tenant->getDecryptedDbPassword();
        $candidatePassword = $decryptedPassword ?: env('TENANT_DEMO_PASSWORD', 'Tenant.1234');

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

        // Intento rápido de conexión para superficiear problemas de credenciales
        try {
            DB::connection('tenant')->select('SELECT 1');
        } catch (\Throwable $e) {
            $tenant->forceFill(['error_message' => $e->getMessage()])->save();
            abort(500, 'Error de conexión a la BD del tenant: ' . $e->getMessage());
        }

        $tenant->makeCurrent();

        try {
            return $next($request);
        } finally {
            $tenant->forgetCurrent();
        }
    }
}
