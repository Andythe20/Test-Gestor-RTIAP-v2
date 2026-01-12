<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';
    protected $connection = 'tenant';

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
