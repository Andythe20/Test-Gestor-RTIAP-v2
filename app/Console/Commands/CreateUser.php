<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

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
                            {--tenant_id= : The tenant ID (optional)}
                            {--is_admin : IsAdmin?}';

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

        $is_admin = $this->option('is_admin');
        $tenantId = $this->option('tenant_id'); // null o string/number

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error("Email inválido: {$email}");

            return 1;
        }

        if (User::on('landlord')->where('email', $email)->exists()) {
            $this->error("User con email {$email} ya existe.");

            return 1;
        }

        $tenant = null;
        if (! $is_admin) {
            if (! $tenantId) {
                $this->error('Falta tenant_id');

                return 1;
            }

            $tenant = Tenant::on('landlord')->find($tenantId);
            if (! $tenant) {
                $this->error("Tenant no encontrado con id {$tenantId}");

                return 1;
            }
        } else {
            $tenantId = null;
        }

        $user = \App\Models\User::on('landlord')->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => $is_admin
            'role' => $is_admin ? 'admin' : 'tenant', // Solo por compatibilidad
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
