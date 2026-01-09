<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin.only' => \App\Http\Middleware\AdminOnly::class,
            'tenant.context' => \App\Http\Middleware\TenantContext::class,

            // opcional (para API con token)
            'admin.token' => \App\Http\Middleware\EnsureAdminToken::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            '/login',
        ]);
    })
    ->withExceptions(function ($exceptions) {
        //
    })
    ->create();
