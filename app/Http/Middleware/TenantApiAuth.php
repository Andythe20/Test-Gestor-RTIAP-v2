<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TenantApiAuth
{
    // Funcion para manejar la autenticacion de API de tenant
    public function handle(Request $request, Closure $next)
    {
        //      --- VERIFICACION DE TOKEN ---
        // Lee el token de autorizacion Bearer de la cabecera
        $auth = $request->bearerToken();

        if (empty($auth)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Espera el formato del token: <tenant_id> | <random>
        $parts = explode('|', $auth, 2);
        if (count($parts) !== 2) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        [$tid, $random] = $parts;

        // Busca el tenant por la id proporcionada por el token
        $tenant = Tenant::on('landlord')->find($tid);
        if (! $tenant) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Si lo encuentra, verifica el token
        if (! $tenant->verifyApiToken($auth)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }



        // Si la ruta contiene un parametro 'tenant' (path) se asegura que coincida con el tenant
        $routeTenant = $request->route('tenant');
        if ($routeTenant) {
            // Si el binding devolvió un modelo compararlo por id/path según corresponda
            if ($routeTenant instanceof \App\Models\Tenant) {
                if ((int) $routeTenant->id !== (int) $tenant->id) {
                    return response()->json(['message' => 'Forbidden - token does not match tenant route'], 403);
                }
            } else {
                // Es probable que venga el path como string
                if ((string) $routeTenant !== (string) $tenant->path) {
                    return response()->json(['message' => 'Forbidden - token does not match tenant path'], 403);
                }
            }
        }

        // Configurar la conexión usando la contraseña descifrada o un fallback (como TenantContext)
        try {
            $decrypted = $tenant->getDecryptedDbPassword();
            $candidatePassword = $decrypted ?: env('TENANT_DEMO_PASSWORD', 'Tenant.1234');

            if (empty($tenant->database) || empty($tenant->db_username) || empty($candidatePassword)) {
                Log::error('Tenant DB credentials missing for API request', ['tenant_id' => $tenant->id]);
                return response()->json(['message' => 'Tenant DB credentials incomplete'], 500);
            }

            $base = config('database.connections.tenant');

            config(['database.connections.tenant' => array_merge($base, [
                'database' => $tenant->database,
                'username' => $tenant->db_username,
                'password' => $candidatePassword,
            ])]);

            \Illuminate\Support\Facades\DB::purge('tenant');
            \Illuminate\Support\Facades\DB::reconnect('tenant');

            // Intento rápido de conexión para surfacear errores de credenciales
            try {
                \Illuminate\Support\Facades\DB::connection('tenant')->select('SELECT 1');
            } catch (\Throwable $e) {
                Log::error('Tenant DB connection test failed in API middleware: ' . $e->getMessage(), ['tenant_id' => $tenant->id]);
                return response()->json(['message' => 'Tenant DB credentials incomplete or invalid'], 500);
            }

            $tenant->makeCurrent();
        } catch (\Throwable $e) {
            Log::error('Failed to make tenant current in API middleware: ' . $e->getMessage(), ['tenant_id' => $tenant->id]);
            return response()->json(['message' => 'Internal Server Error'], 500);
        }

        // Ajunta el tenant a los atributos de la solicitud para que el controlador lo use
        $request->attributes->set('tenant', $tenant);

        return $next($request);
    }
}
