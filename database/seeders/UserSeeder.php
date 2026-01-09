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
        // Seeder para crear un usuario SuperAdmin (ejecutar 1 sola vez)
        User::create([
            'name' => 'SuperAdmin',
            'email' => 'superadmin@test.cl',
            'password' => bcrypt('test1234'),
            'database' => null
        ]);
    }
}
