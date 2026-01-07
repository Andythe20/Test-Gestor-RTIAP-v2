<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarjeta extends Model
{
    protected $table = 'tarjetas';
    protected $connection = 'tenant';

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
