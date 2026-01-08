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
                            {--tenant_id= : The tenant ID (optional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user with properly hashed password';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');
        $tenantId = $this->option('tenant_id');

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            $this->error("User with email {$email} already exists!");
            return 1;
        }

        // Create the user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'tenant_id' => $tenantId,
            'database' => null, // Will be set if tenant is specified
        ]);

        // If tenant is specified, update database field
        if ($tenantId) {
            $tenant = \App\Models\Tenant::find($tenantId);
            if ($tenant) {
                $user->database = $tenant->database_name;
                $user->save();
            }
        }

        $this->info("User {$name} created successfully with email {$email}");

        return 0;
    }
}
