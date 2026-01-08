<?php

use App\Http\Controllers\Admin\TenantsController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->tenant_id) {
            return redirect('/tenant/dashboard');
        } else {
            return redirect('/admin/tenants');
        }
    }
    return redirect('/login');
});

Route::get('/admin/tenants', [TenantsController::class, 'showAdminView'])->name('admin.tenants.index');
Route::get('/admin/tenants/{id}', [TenantsController::class, 'show'])->name('admin.tenants.show');
Route::post('/admin/tenants', [TenantsController::class, 'store'])->name('admin.tenants.store');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/admin/tenants', [TenantsController::class, 'showAdminView'])->name('admin.tenants.index');
    Route::get('/admin/tenants/{id}', [TenantsController::class, 'show'])->name('admin.tenants.show');
    Route::post('/admin/tenants', [TenantsController::class, 'store'])->name('admin.tenants.store');

    Route::get('/tenant/dashboard', function () {
        return inertia('Tenant/Dashboard');
    })->name('tenant.dashboard');
});
