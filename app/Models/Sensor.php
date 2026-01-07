<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    /** @use HasFactory<\Database\Factories\SensorFactory> */
    use HasFactory;

    protected $table = 'sensores';
    protected $connection = 'tenant';

    public function sistema()
    {
        return $this->belongsTo(Sistema::class);
    }
}
