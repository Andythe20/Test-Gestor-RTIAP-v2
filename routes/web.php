<?php

use App\Http\Controllers\Admin\TenantsController;
use App\Http\Controllers\AuthController;
use App\Models\Tenant;
use App\Models\Empresa;
use App\Models\Empleado;
use App\Models\Producto;
use App\Models\Tarjeta;
use App\Models\Venta;
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
        Route::get('/tenants/{tenant:id}', [TenantsController::class, 'show'])->name('admin.tenants.show');
        Route::post('/tenants/{tenant:id}/seed', [TenantsController::class, 'seed'])->name('admin.tenants.seed');
    });

/*
    Tenant
 */

Route::prefix('t/{tenant:path}')
    ->middleware(['auth', 'tenant.context'])
    ->group(function () {

        Route::get('/', function (Tenant $tenant) {
            // Gracias al middleware tenant.context, las consultas usan la conexión del tenant.
            $empresa = Empresa::query()->first();
            $empleados = Empleado::query()->orderByDesc('created_at')->limit(10)->get();
            $productos = Producto::query()->orderByDesc('created_at')->limit(10)->get();
            $tarjetas = Tarjeta::query()->orderByDesc('created_at')->limit(10)->get();
            $ventas = Venta::query()
                ->with(['empleado', 'producto', 'tarjeta'])
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();

            $estadisticas = [
                'empleados' => Empleado::query()->count(),
                'productos' => Producto::query()->count(),
                'ventas' => Venta::query()->count(),
            ];

            return inertia('Tenant/Dashboard', [
                'tenant' => $tenant->path,
                'empresa' => $empresa,
                'empleados' => $empleados,
                'productos' => $productos,
                'tarjetas' => $tarjetas,
                'ventas' => $ventas,
                'estadisticas' => $estadisticas,
            ]);
        })->name('tenant.home');
    });
