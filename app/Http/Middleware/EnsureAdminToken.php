<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdminToken
{
    public function handle($request, \Closure $next)
    {
        $expected = env('ADMIN_TOKEN');

        if (!is_string($expected) || $expected === '') {
            return response()->json(['message' => 'Server misconfigured: ADMIN_TOKEN missing'], 500);
        }

        $provided = (string) ($request->header('X-ADMIN-TOKEN') ?? '');

        if ($provided === '' || !hash_equals($expected, $provided)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
