<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class TenantProvisioner
{
    public function provision(Tenant $tenant): void
    {
        if (empty($tenant->database)) {
            $tenant->database = $tenant->name.'_DB';
        }

        $dbName = $tenant->database;

        if (empty($tenant->db_username)) {
            $tenant->db_username = $dbName.'_app';
        }

        $demoPassword = 'Tenant.1234';
        $mysqlUserHost = env('TENANT_MYSQL_USER_HOST', 'localhost');

        DB::connection('provisioner')->statement("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        DB::connection('provisioner')->statement("CREATE USER IF NOT EXISTS '{$tenant->db_username}'@'{$mysqlUserHost}' IDENTIFIED BY '{$demoPassword}'");

        DB::connection('provisioner')->statement(
            "ALTER USER '{$tenant->db_username}'@'{$mysqlUserHost}' IDENTIFIED BY '{$demoPassword}'"
        );

        DB::connection('provisioner')->statement("
            GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX, REFERENCES
            ON `$dbName`.*
            TO '{$tenant->db_username}'@'{$mysqlUserHost}'
        ");

        DB::connection('provisioner')->statement('FLUSH PRIVILEGES');

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
