<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sistema extends Model
{
    /** @use HasFactory<\Database\Factories\SistemaFactory> */
    use HasFactory;

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function sensores()
    {
        return $this->hasMany(Sensor::class);
    }
}
