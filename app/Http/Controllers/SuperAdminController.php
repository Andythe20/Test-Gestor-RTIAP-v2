<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Sucursal;
use Inertia\Inertia;

class SuperAdminController extends Controller
{
    // Vista Principal: Lista de todos los Clientes
    public function index()
    {
        // Esto consulta la BD Central (Landlord)
        $tenants = Tenant::all();

        return Inertia::render('SuperAdmin/Index', [
            'tenants' => $tenants
        ]);
    }

    // Vista de Detalle: Ver qué tiene un Cliente por dentro
    public function show($id)
    {
        // 1. Buscamos al tenant en la BD Central
        $tenant = Tenant::findOrFail($id);

        // 2. ¡MAGIA! Nos conectamos a SU base de datos
        $tenant->makeCurrent();

        // 3. Ahora 'Sucursal' busca en la BD del cliente, no en la central
        $sucursales = Sucursal::with('sistemas.sensores')->get();

        // 4. (Opcional) Volver a la central, aunque al terminar la request Laravel limpia solo.
        // $tenant->forgetCurrent();

        return Inertia::render('SuperAdmin/Show', [
            'tenant' => $tenant,
            'sucursales' => $sucursales
        ]);
    }
}
