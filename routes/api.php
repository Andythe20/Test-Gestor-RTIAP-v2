<?php

use App\Http\Controllers\Admin\TenantsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Api\ProductosController;
use App\Http\Middleware\EnsureAdminToken;
use App\Http\Middleware\TenantApiAuth;
use Illuminate\Support\Facades\Route;

/**
 * --- ADMIN ---
 */
Route::middleware([EnsureAdminToken::class])->group(function () {
    Route::post('/admin/tenants', [TenantsController::class, 'store']);
    Route::get('/admin/tenants', [TenantsController::class, 'index']);
    Route::post('/admin/users', [UsersController::class, 'store']);
    Route::get('/admin/tenants/{tenant:path}/users', [TenantsController::class, 'users']); // para obtener usuarios de un tenant
    Route::get('/admin/tenants/{tenant}', [TenantsController::class, 'show']);
    Route::post('/admin/tenants/{tenant}/seed', [TenantsController::class, 'seed']);
});
