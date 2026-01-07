<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Empresa;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    // Vista Principal: Lista de todos los Clientes
    public function index()
    {
        // Esto consulta la BD Central (Landlord)
        $tenants = Tenant::all();

        return Inertia::render('SuperAdmin/Index', ['tenants' => $tenants]);
    }

    // Vista de Detalle: Ver qué tiene un Cliente por dentro
    public function show($id)
    {
        // 1. Buscamos al tenant en la BD Central
        $tenant = Tenant::findOrFail($id);

        // 2. Le decimos a Laravel explícitamente qué base de datos usar
        config(['database.connections.tenant.database' => $tenant->database_name]);

        // 3. Purgamos la conexión 'tenant' vieja (que apuntaba a null)
        DB::purge('tenant');

        // 4. Reconectamos con la nueva configuración
        DB::reconnect('tenant');

        // 5. Ahora podemos usar los modelos normalmente, y estos usarán la BD del tenant
        $empresas = Empresa::with('sucursales.sistemas.sensores')->get();

        return Inertia::render('SuperAdmin/Show', [
            'tenant' => $tenant,
            'empresas' => $empresas
        ]);
    }
}
