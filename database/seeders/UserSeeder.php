<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super admin user
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.cl',
            'password' => bcrypt('superadmin'),
            'database' => null,
            'tenant_id' => null,
        ]);

        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'database' => null,
            'tenant_id' => null,
        ]);

        // Create tenant users for existing tenants
        $tenants = Tenant::all();
        foreach ($tenants as $tenant) {
            User::create([
                'name' => $tenant->name . ' User',
                'email' => 'user@' . $tenant->domain,
                'password' => bcrypt('password'),
                'database' => $tenant->database,
                'tenant_id' => $tenant->id,
            ]);
        }
    }
}
