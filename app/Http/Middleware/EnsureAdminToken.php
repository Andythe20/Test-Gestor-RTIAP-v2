<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdminToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('X-ADMIN-TOKEN');
        if (!$token || !hash_equals(config('app.admin_token'), $token)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return $next($request);
    }
}
