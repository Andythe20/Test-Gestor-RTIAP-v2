<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarjeta extends Model
{
    use HasFactory;

    protected $table = 'tarjetas';
    protected $connection = 'tenant';

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
