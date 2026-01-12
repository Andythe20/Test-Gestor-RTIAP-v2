<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';
    protected $connection = 'tenant';

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function tarjeta()
    {
        return $this->belongsTo(Tarjeta::class);
    }
}
