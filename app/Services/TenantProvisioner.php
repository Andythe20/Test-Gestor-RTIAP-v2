<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class TenantProvisioner
{
    public function provision(Tenant $tenant): void
    {
        $normalizedName = strtolower($tenant->name);
        $normalizedName = preg_replace('/\s+/', '_', $normalizedName);
        $normalizedName = preg_replace('/[^a-z0-9_]/', '', $normalizedName);
        $normalizedName = preg_replace('/_+/', '_', $normalizedName);
        $normalizedName = trim($normalizedName, '_');

        if (empty($tenant->database)) {
            $tenant->database = $normalizedName . '_db';
        }

        if (empty($tenant->db_username)) {
            $tenant->db_username = $normalizedName . '_app';
        }

        $dbName = $tenant->database;
        $dbUsername = $tenant->db_username;
        $demoPassword = 'Tenant.1234';
        $mysqlUserHost = env('TENANT_MYSQL_USER_HOST', 'localhost');

        // Validación fuerte (identificadores SQL)
        if (! preg_match('/^[a-z0-9_]{1,64}$/', $dbName)) {
            throw new \RuntimeException("DB inválida: {$dbName}");
        }
        if (! preg_match('/^[a-z0-9_]{1,64}$/', $dbUsername)) {
            throw new \RuntimeException("Usuario MySQL inválido: {$dbUsername}");
        }

        DB::connection('provisioner')->statement("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        DB::connection('provisioner')->statement("CREATE USER IF NOT EXISTS '{$dbUsername}'@'{$mysqlUserHost}' IDENTIFIED BY '{$demoPassword}'");

        DB::connection('provisioner')->statement(
            "ALTER USER '{$tenant->db_username}'@'{$mysqlUserHost}' IDENTIFIED BY '{$demoPassword}'"
        );

        DB::connection('provisioner')->statement("
            GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX, REFERENCES
            ON `$dbName`.*
            TO '{$dbUsername}'@'{$mysqlUserHost}'
        ");

        $tenant->status = 'provisioning';
        $tenant->save();

        $this->migrateTenant($tenant, $demoPassword);

        $tenant->status = 'active';
        $tenant->save();
    }

    private function migrateTenant(Tenant $tenant, string $demoPassword): void
    {

        config(['database.connections.tenant' => [
            'driver' => 'mysql',
            'host' => env('TENANT_DB_HOST', env('DB_HOST', '127.0.0.1')),
            'port' => env('TENANT_DB_PORT', env('DB_PORT', '3306')),
            'database' => $tenant->database,
            'username' => $tenant->db_username,
            'password' => $demoPassword,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'strict' => true,
        ]]);

        DB::purge('tenant');
        DB::reconnect('tenant');

        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant',
            '--force' => true,
        ]);
    }
}
