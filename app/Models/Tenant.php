<?php

namespace App\Models;

use Spatie\Multitenancy\Models\Tenant as BaseTenant;

//class Tenant extends Model
class Tenant extends BaseTenant
{
    protected $connection = 'landlord';

    protected $fillable = [
        'name',
        'domain',
        'database',
        'db_username',
        'db_password_encrypted',
        'status',
    ];
}
