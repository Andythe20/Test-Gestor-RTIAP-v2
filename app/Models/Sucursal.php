<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    /** @use HasFactory<\Database\Factories\SucursalFactory> */
    use HasFactory;

    protected $table = 'sucursales';
    protected $connection = 'tenant';

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sistemas()
    {
        return $this->hasMany(Sistema::class);
    }
}
