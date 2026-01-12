<?php

use App\Http\Controllers\Admin\TenantsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Middleware\EnsureAdminToken;
use Illuminate\Support\Facades\Route;

Route::middleware([EnsureAdminToken::class])->group(function () {
    Route::post('/admin/tenants', [TenantsController::class, 'store']);
    Route::get('/admin/tenants', [TenantsController::class, 'index']);
    Route::post('/admin/users', [UsersController::class, 'store']);
});
