<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    /** @use HasFactory<\Database\Factories\EmpresaFactory> */
    use HasFactory;

    protected $table = 'empresas';
    protected $connection = 'tenant';

    public function sucursales()
    {
        return $this->hasMany(Sucursal::class);
    }
}
