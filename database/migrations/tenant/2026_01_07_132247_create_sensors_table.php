<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sensores', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_serie')->unique();
            $table->foreignId('sistema_id')->constrained('sistemas')->onDelete('cascade');
            $table->string('ubicacion'); // Ej: "Fondo derecha"
            $table->float('valor_actual'); // Ej: -5.4
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensors');
    }
};
