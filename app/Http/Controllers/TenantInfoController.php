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

        // Verificar si la consulta viene de una peticion API o Web
        if (!$request->header('X-Inertia')) {
            if (empty($dataPayload)) {
                return response()->json([
                    'message' => 'No data found for tenant',
                ], 203);
            }
            return response()->json($dataPayload);
        }

        return Inertia::render('Tenant/Dashboard', [
            'tenant' => $tenant,
            'data' => $dataPayload,
        ]);
    }
}
