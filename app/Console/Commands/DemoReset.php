<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DemoReset extends Command
{
    protected $signature = 'demo:reset';

    protected $description = 'Reset demo: drop tenant DBs + drop users + migrate:fresh landlord';

    public function handle(): int
    {
        if (app()->environment('production')) {
            $this->error('Not allowed in production.');

            return self::FAILURE;
        }

        $deny = ['mysql', 'information_schema', 'performance_schema', 'sys', 'landlord'];

        $tenantRows = collect();
        if (Schema::connection('landlord')->hasTable('tenants')) {
            $tenantRows = collect(
                DB::connection('landlord')->table('tenants')
                    ->select('database', 'db_username')
                    ->whereNotNull('database')
                    ->get()
            );
        }

        $tenantDbFromTable = $tenantRows->pluck('database')->filter()->unique()->values();

        $dbsWithTenantTables = collect(DB::connection('provisioner')->select("
            SELECT table_schema AS db
            FROM information_schema.tables
            WHERE table_name IN ('migrations','projects')
            GROUP BY table_schema
            HAVING SUM(table_name='migrations') > 0 AND SUM(table_name='projects') > 0
        "))->map(fn ($r) => (string) ($r->db ?? ''))->filter()->unique()->values();

        $candidateDbs = $tenantDbFromTable
            ->merge($dbsWithTenantTables)
            ->filter()
            ->unique()
            ->values();

        $usernames = $tenantRows
            ->map(fn ($r) => $r->db_username ?: (($r->database ?? '').'_app'))
            ->filter()
            ->unique()
            ->values();

        foreach ($candidateDbs as $dbName) {
            $dbName = trim((string) $dbName);

            if ($dbName === '' || in_array(strtolower($dbName), $deny, true)) {
                continue;
            }

            if (! preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $dbName)) {
                continue;
            }

            DB::connection('provisioner')->statement("DROP DATABASE IF EXISTS `{$dbName}`");
            $usernames->push($dbName.'_app');
        }

        $usernames = $usernames->filter()->unique()->values();

        foreach ($usernames as $username) {
            $username = trim((string) $username);

            if ($username === '' || strlen($username) > 32) {
                continue;
            }

            if (! preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $username)) {
                continue;
            }

            $hosts = DB::connection('provisioner')->select(
                'SELECT Host FROM mysql.user WHERE User = ?',
                [$username]
            );

            foreach ($hosts as $h) {
                $host = (string) ($h->Host ?? '');
                if ($host === '' || ! preg_match('/^[a-zA-Z0-9\.\-%_]+$/', $host)) {
                    continue;
                }
                DB::connection('provisioner')->statement("DROP USER IF EXISTS '{$username}'@'{$host}'");
            }
        }

        $this->callSilent('optimize:clear');

        $exit = Artisan::call('migrate:fresh', [
            '--database' => 'landlord',
            '--path' => [
                base_path('database/migrations'),           // aquí está add_password_to_users_table.php
                base_path('database/migrations/landlord'),  // aquí están tus migraciones landlord
            ],
            '--realpath' => true,
            '--force' => true,
            '--seed' => true, // opcional: si quieres que se ejecute DatabaseSeeder
        ]);

        $this->line(Artisan::output());

        return $exit === 0 ? self::SUCCESS : self::FAILURE;

        return $exit === 0 ? self::SUCCESS : self::FAILURE;
    }
}
