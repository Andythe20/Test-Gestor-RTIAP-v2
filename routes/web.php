<?php

use App\Http\Controllers\Admin\TenantsController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Si no estás logeado, manda al login tenant (o a una landing)
    if (! Auth::check()) {
        return redirect()->route('tenant.login'); // ver abajo
    }

    // Si estás logeado, decide según "tipo"
    $user = Auth::user();

    return $user->tenant_id
        ? redirect()->route('tenant.dashboard', ['tenant' => $user->database ?? '']) // ajusta según tu app
        : redirect()->route('admin.dashboard');
});

/**
 * admin (LANDLORD)
 */
Route::middleware('guest')->group(function () {
    Route::get('/adminlogin', [AuthController::class, 'showLogin'])
        ->name('admin.login');

    Route::post('/adminlogin', [AuthController::class, 'login'])
        ->name('admin.login.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/admin/logout', [AuthController::class, 'logout'])
        ->name('admin.logout');

    Route::get('/admin/dashboard', [TenantsController::class, 'showAdminView'])
        ->name('admin.dashboard');

    Route::post('/admin/tenants', [TenantsController::class, 'store'])
        ->name('admin.tenants.store');

    Route::get('/admin/tenants/{tenant}', [TenantsController::class, 'show'])
        ->name('admin.tenants.show');
});

/**
 * TENANT (PATH)
 * Ej: /t/acme/login, /t/acme/dashboard
 */
Route::prefix('t/{tenant}')
    ->middleware(['tenant']) // NeedsTenant de Spatie
    ->group(function () {

        Route::middleware('guest')->group(function () {
            Route::get('/login', [AuthController::class, 'showLogin'])
                ->name('tenant.login');

            Route::post('/login', [AuthController::class, 'login'])
                ->name('tenant.login.post');
        });

        Route::middleware('auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout'])
                ->name('tenant.logout');

            Route::get('/dashboard', function (string $tenant) {
                return inertia('Tenant/Dashboard', ['tenant' => $tenant]);
            })->name('tenant.dashboard');
        });
    });
