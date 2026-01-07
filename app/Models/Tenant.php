<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Tenant as SpatieTenant; // Si usas el paquete Spatie

class Tenant extends SpatieTenant
{
    use HasFactory;

    // Importante: Este modelo vive en la base de datos central
    protected $connection = 'landlord';

    protected $table = 'tenants';
    protected $fillable = ['name', 'domain', 'database_name'];
}
