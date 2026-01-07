<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\Sistema;
use App\Models\Sensor;

class LandlordSeeder extends Seeder
{
    public function run(): void
    {
        // --------------------------------------------------
        // CLIENTE 1: Walmart
        // --------------------------------------------------
        echo "🏗️  Creando Cliente: Walmart...\n";

        $walmart = Tenant::create([
            'name' => 'Walmart Chile',
            'domain' => 'walmart.localhost',
            'database_name' => 'tenant_walmart_db',
        ]);

        $walmart->makeCurrent();

        echo "   🌱 Sembrando datos para Walmart...\n";

        // JERARQUÍA: Empresa -> Sucursal -> Sistema -> Sensor

        // 1. Creamos la Empresa (Ej: Walmart Alimentos)
        Empresa::factory()->count(1)
            ->has(
                // 2. Esa empresa tiene Sucursales
                Sucursal::factory()->count(2)
                    ->has(
                        // 3. Esa sucursal tiene Sistemas
                        Sistema::factory()->count(3)
                            ->has(
                                // 4. Ese sistema tiene Sensores
                                Sensor::factory()->count(5),
                                'sensores'
                            ),
                        'sistemas'
                    ),
                'sucursales'
            )
            ->create();

        $walmart->forgetCurrent();

        // --------------------------------------------------
        // CLIENTE 2: Agrosuper
        // --------------------------------------------------
        echo "🏗️  Creando Cliente: Agrosuper...\n";

        $agrosuper = Tenant::create([
            'name' => 'Agrosuper Industrial',
            'domain' => 'agrosuper.localhost',
            'database_name' => 'tenant_agrosuper_db',
        ]);

        $agrosuper->makeCurrent();

        echo "   🌱 Sembrando datos para Agrosuper...\n";

        Empresa::factory()->count(1)
            ->has(
                Sucursal::factory()->count(1) // Planta Principal
                    ->has(
                        Sistema::factory()->count(5)
                            ->has(
                                Sensor::factory()->count(8),
                                'sensores'
                            ),
                        'sistemas'
                    ),
                'sucursales'
            )
            ->create();

        $agrosuper->forgetCurrent();

        echo "✅ ¡Proceso terminado! Jerarquía completa creada.\n";
    }
}
