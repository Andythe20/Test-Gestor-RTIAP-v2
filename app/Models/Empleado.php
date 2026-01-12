<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    protected $table = 'empleados';
    protected $connection = 'tenant';

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
