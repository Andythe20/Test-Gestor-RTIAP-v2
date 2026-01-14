<?php

use App\Http\Controllers\Admin\TenantsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Api\ProductosController;
use App\Http\Controllers\TenantInfoController;
use App\Http\Middleware\EnsureAdminToken;
use App\Http\Middleware\TenantApiAuth;
use App\Http\Middleware\TenantContext;
use Illuminate\Support\Facades\Route;

/**
 * --- ADMIN ---
 */
Route::middleware([EnsureAdminToken::class])->group(function () {
    Route::post('/admin/tenants', [TenantsController::class, 'store']);
    Route::get('/admin/tenants', [TenantsController::class, 'index']); // obtener lista de tenants
    Route::post('/admin/users', [UsersController::class, 'store']);
    Route::get('/admin/tenants/{tenant:path}/users', [TenantsController::class, 'users']); // para obtener usuarios de un tenant
    Route::get('/admin/tenants/{tenant:path}', [TenantsController::class, 'show']); // obtener info de un tenant
    Route::post('/admin/tenants/{tenant:path}/seed', [TenantsController::class, 'seed']);
});

/**
 * --- TENANTS ---
 */
Route::middleware([TenantApiAuth::class])->group(function () {
    // Datos de tenant
    Route::get('/t/{tenant:path}/', [TenantInfoController::class, 'showInfo']);
});
