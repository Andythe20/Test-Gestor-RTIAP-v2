<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    protected $connection = 'tenant';

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
