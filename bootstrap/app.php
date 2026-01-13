<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin.only' => \App\Http\Middleware\AdminOnly::class,
            'tenant.context' => \App\Http\Middleware\TenantContext::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'admin.token' => \App\Http\Middleware\EnsureAdminToken::class,
            'tenant.api' => \App\Http\Middleware\TenantApiAuth::class,
        ]);

        // Ensure the XSRF-TOKEN cookie is not encrypted so client-side code
        // can read and send the raw token in the X-XSRF-TOKEN header. If the
        // cookie is encrypted the header would contain the encrypted value
        // and the CSRF check would fail with a 419 on initial requests.
        $middleware->encryptCookies(except: [
            'XSRF-TOKEN',
        ]);

        $middleware->validateCsrfTokens(except: [
            '/login',
            '/logout',
        ]);
    })
    ->withExceptions(function ($exceptions) {
        //
    })
    ->create();
