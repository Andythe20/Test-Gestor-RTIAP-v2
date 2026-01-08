<?php

use App\Http\Controllers\Admin\TenantsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin/tenants');
});

Route::get('/admin/tenants', [TenantsController::class, 'showAdminView'])->name('admin.tenants.index');
Route::post('/admin/tenants', [TenantsController::class, 'store'])->name('admin.tenants.store');
