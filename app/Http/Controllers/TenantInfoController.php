<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\Empresa;
use App\Models\Producto;
use App\Models\Tarjeta;
use App\Models\Tenant;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TenantInfoController extends Controller
{
    public function showInfo(Request $request, Tenant $tenant)
    {
        // cargamos los datos y lo montamos en un payload
        $empresa = Empresa::query()->first();
        $empleados = Empleado::query()->orderByDesc('created_at')->limit(10)->get();
        $productos = Producto::query()->orderByDesc('created_at')->limit(10)->get();
        $ventas = Venta::query()->orderByDesc('created_at')->limit(10)->get();
        $tarjetas = Tarjeta::query()->orderByDesc('created_at')->limit(10)->get();

        $dataPayload = [
            'empresa' => $empresa,
            'empleados' => $empleados,
            'productos' => $productos,
            'ventas' => $ventas,
            'tarjetas' => $tarjetas,
        ];

        // If this is an Inertia SPA navigation, use Inertia::render so the
        // Inertia middleware returns a proper Inertia JSON payload (with headers).
        if ($this->isInertiaRequest($request)) {
            return Inertia::render('Tenant/Dashboard', [
                'tenant' => $tenant,
                'data' => $dataPayload,
            ]);
        }

        // API clients that explicitly want JSON
        if ($request->wantsJson()) {
            if (empty($dataPayload)) {
                return response()->json([
                    'message' => 'No data found for tenant',
                ], 203);
            }
            return response()->json($dataPayload);
        }

        // Regular full page load
        return Inertia::render('Tenant/Dashboard', [
            'tenant' => $tenant,
            'data' => $dataPayload,
        ]);
    }
}
