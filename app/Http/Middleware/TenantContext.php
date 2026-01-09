<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;

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
        if ($user->role !== 'admin' && (int) $user->tenant_id !== (int) $tenant->id) {
            abort(403);
        }

        $tenant->makeCurrent();

        try {
            return $next($request);
        } finally {
            $tenant->forgetCurrent();
        }
    }
}
