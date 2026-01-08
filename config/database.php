<?php

use Illuminate\Support\Str;

return [
    'default' => env('DB_CONNECTION', 'landlord'),

    'connections' => [
        'landlord' => [
            'driver' => 'mysql',
            'host' => env('LANDLORD_DB_HOST', env('DB_HOST', '127.0.0.1')),
            'port' => env('LANDLORD_DB_PORT', env('DB_PORT', 3306)),
            'database' => env('LANDLORD_DB_DATABASE', env('DB_DATABASE', 'landlord')),
            'username' => env('LANDLORD_DB_USERNAME', env('DB_USERNAME', 'landlord_app')),
            'password' => env('LANDLORD_DB_PASSWORD', env('DB_PASSWORD', '')),
        ],

        'tenant' => [
            'driver' => 'mysql',
            'host' => env('TENANT_DB_HOST', '127.0.0.1'),
            'port' => env('TENANT_DB_PORT', 3306),

            // se llena en el runtime
            'database' => null,
            'username' => null,
            'password' => null,
        ],
        'provisioner' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            // By default fall back to the main DB credentials so provisioning works
            // locally without requiring a dedicated MySQL user. For production it's
            // recommended to set explicit PROVISIONER_DB_* variables in your .env
            'database' => env('PROVISIONER_DB_DATABASE', 'mysql'),
            /* 'username' => env('PROVISIONER_DB_USERNAME', 'tenant_provisioner'),
            'password' => env('PROVISIONER_DB_PASSWORD', ''), */
            'username' => env('PROVISIONER_DB_USERNAME', env('DB_USERNAME', 'root')),
            'password' => env('PROVISIONER_DB_PASSWORD', env('DB_PASSWORD', '')),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'strict' => true,
        ],
    ],
];
