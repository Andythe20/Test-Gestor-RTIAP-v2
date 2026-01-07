<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DemoReset extends Command
{
    protected $signature = 'demo:reset {--drop-tenants} {--force}';
    protected $description = 'Reset demo: landlord fresh + optional drop tenant DBs';

    public function handle(): int
    {
        if (app()->environment('production')) {
            $this->error('Not allowed in production.');
            return self::FAILURE;
        }


        $rows = DB::connection('provisioner')->select("SHOW DATABASES LIKE 'tenant%'");

        foreach ($rows as $row) {
            $dbName = array_values((array) $row)[0] ?? null;

            if (!is_string($dbName)) {
                continue;
            }

            if (!preg_match('/^tenant\d+$/', $dbName)) {
                continue;
            }

            DB::connection('provisioner')->statement("DROP DATABASE IF EXISTS `{$dbName}`");
        }

        $this->callSilent('optimize:clear');

        $exit = Artisan::call('migrate:fresh', [
            '--database' => 'landlord',
            '--path' => base_path('database/migrations/landlord'),
            '--realpath' => true,
            '--force' => true,
        ]);

        $this->line(Artisan::output());

        return $exit === 0 ? self::SUCCESS : self::FAILURE;
    }
}
