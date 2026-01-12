<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\Empleado;
use App\Models\Producto;
use App\Models\Tarjeta;
use App\Models\Venta;

class TenantDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Empresa principal
        $empresa = Empresa::factory()->create();

        // Empleados
        $empleados = Empleado::factory()->count(5)->create();

        // Productos
        $productos = Producto::factory()->count(8)->create();

        // Tarjetas (ejemplo simple: 1 tarjeta)
        $tarjeta = Tarjeta::factory()->create();

        // Ventas de algunos productos con la tarjeta creada
        foreach ($productos->take(5) as $producto) {
            Venta::factory()->create([
                'empleado_id' => $empleados->random()->id,
                'producto_id' => $producto->id,
                'tarjeta_id' => $tarjeta->id,
            ]);
        }
    }
}
