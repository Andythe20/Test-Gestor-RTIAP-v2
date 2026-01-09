<?php

use App\Http\Controllers\Admin\TenantsController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

/*
    AUTH PARA LOGIN
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
    Admin
 */
Route::prefix('admin')
    ->middleware(['auth', 'admin.only'])
    ->group(function () {
        Route::get('/', [TenantsController::class, 'showAdminView'])->name('admin.home');

        Route::post('/tenants', [TenantsController::class, 'store'])->name('admin.tenants.store');
        Route::get('/tenants/{tenant}', [TenantsController::class, 'show'])->name('admin.tenants.show');
    });

/*
    Tenant
 */

Route::prefix('t/{tenant}')
    ->middleware(['auth', 'tenant.context'])
    ->group(function () {
        Route::get('/', fn($tenant) => inertia('Tenant/Dashboard', ['tenant' => $tenant]))
            ->name('tenant.home');
    });
