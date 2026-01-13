<?php

use App\Http\Controllers\Admin\TenantsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\EmpleadosController;
use App\Http\Controllers\Api\ProductosController;
use App\Http\Middleware\EnsureAdminToken;
use App\Http\Middleware\TenantApiAuth;
use Illuminate\Support\Facades\Route;

Route::middleware([EnsureAdminToken::class])->group(function () {
    Route::post('/admin/tenants', [TenantsController::class, 'store']);
    Route::get('/admin/tenants', [TenantsController::class, 'index']);
    Route::post('/admin/users', [UsersController::class, 'store']);
});

// Rutas API para tenants
Route::middleware([TenantApiAuth::class])->group(function () {
    Route::get('/{path}/productos', [ProductosController::class, 'index']);
    Route::get('/{path}/productos/{id}', [ProductosController::class, 'show']);
});
