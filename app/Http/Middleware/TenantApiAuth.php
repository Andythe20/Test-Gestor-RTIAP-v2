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

        // Si la ruta contiene un parametro path, se asegura que coincida con el tenant
        $pathParam = $request->route('path');
        if ($pathParam && $tenant->path !== $pathParam) {
            return response()->json(['message' => 'Forbidden - token does not match tenant path'], 403);
        }

        // Si todo es correcto, se establece el tenant actual usando makeCurrent()
        try {
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
