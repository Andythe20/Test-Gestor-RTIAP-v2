<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdminController;

// Rutas del Landlord (Super Admin)
Route::get('/', [SuperAdminController::class, 'index'])->name('admin.index');
Route::get('/admin/tenant/{id}', [SuperAdminController::class, 'show'])->name('admin.tenant.show');
