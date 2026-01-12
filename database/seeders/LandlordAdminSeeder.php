<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LandlordAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('SEED_ADMIN_EMAIL', 'superadmin@test.com');
        $password = env('SEED_ADMIN_PASSWORD', 'hola123');

        User::on('landlord')->updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Admin',
                'password' => Hash::make($password),
                'is_admin' => 1,
                'role' => 'admin',
                'tenant_id' => null,
                'database' => null,
            ]
        );
    }
}
