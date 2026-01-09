<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-user
                            {name : The name of the user}
                            {email : The email of the user}
                            {password : The password for the user}
                            {--tenant_id= : The tenant ID (optional)}';

    /**
     * The console command description.
     *
     * @var string
     */

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = trim((string) $this->argument('name'));
        $email = $this->argument('email'); // puede venir null
        $password = (string) $this->argument('password');

        $tenantId = $this->option('tenant_id'); // null o string/number
        $role = $this->option('role');          // admin|tenant|null

        if (! $role) {
            $role = $tenantId ? 'tenant' : 'admin';
        }

        if (! in_array($role, ['admin', 'tenant'], true)) {
            $this->error("Role inválido: {$role}. Usa admin o tenant.");

            return 1;
        }

        $tenant = null;
        if ($role === 'tenant') {
            if (! $tenantId) {
                $this->error('Para role=tenant debes pasar --tenant_id=ID');

                return 1;
            }

            $tenant = \App\Models\Tenant::on('landlord')->find($tenantId);
            if (! $tenant) {
                $this->error("Tenant no encontrado con id {$tenantId}");

                return 1;
            }
        } else {
            $tenantId = null;
        }

        // Crear en landlord explícitamente
        if (\App\Models\User::on('landlord')->where('email', $email)->exists()) {
            $this->error("User con email {$email} ya existe.");

            return 1;
        }

        $user = \App\Models\User::on('landlord')->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'tenant_id' => $tenantId,
            'database' => $tenant?->database,
        ]);

        $this->info("User creado: {$user->email} (role={$role})");
        if ($tenant) {
            $this->info("   Tenant: {$tenant->name} (id={$tenant->id}, path={$tenant->path})");
        }

        return 0;
    }
}
