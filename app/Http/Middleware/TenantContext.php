<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;

class TenantContext
{
    public function handle(Request $request, Closure $next)
    {
        /** @var Tenant $tenant */
        $tenant = $request->route('tenant');
        if (! $tenant) {
            abort(404);
        }

        $user = $request->user();
        if (! $user || $user->tenant_id !== $tenant->id) {
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
