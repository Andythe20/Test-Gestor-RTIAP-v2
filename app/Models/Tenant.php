<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Tenant as SpatieTenant; // Si usas el paquete Spatie
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class Tenant extends SpatieTenant
{
    use HasFactory;

    // Importante: Este modelo vive en la base de datos central
    protected $connection = 'landlord';

    protected $table = 'tenants';
    protected $fillable = ['name', 'domain', 'database_name'];

    protected static function booted()
    {
        static::created(function ($tenant) {
            // 1. Crear la Base de Datos física (Usando Landlord)
            \Illuminate\Support\Facades\DB::connection('landlord')->statement("CREATE DATABASE IF NOT EXISTS `{$tenant->database_name}`");

            echo "Base de datos {$tenant->database_name} creada.\n";

            // ---------------------------------------------------------
            // 2. EL FIX: Inyección Manual de Configuración
            // ---------------------------------------------------------
            // No confiamos solo en makeCurrent(). Le decimos a Laravel explícitamente:
            // "La conexión 'tenant' ahora apunta a ESTA base de datos".
            config(['database.connections.tenant.database' => $tenant->database_name]);

            // 3. Purgar y Reconectar para que tome el cambio
            \Illuminate\Support\Facades\DB::purge('tenant');
            \Illuminate\Support\Facades\DB::reconnect('tenant');

            echo "Conexión 'tenant' apuntando a: " . config('database.connections.tenant.database') . "\n";
            echo "Migrando tablas...\n";

            // 4. Ejecutar las migraciones
            \Illuminate\Support\Facades\Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/migrations/tenant',
                '--force' => true,
            ]);

            echo "¡Tablas migradas exitosamente para {$tenant->name}!\n";
        });
    }
}
