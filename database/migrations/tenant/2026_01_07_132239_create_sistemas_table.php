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
        Schema::create('sistemas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Ej: "Túnel 01: Congelados Carnes"
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade');
            $table->string('direccion'); // Ej: "Av. Siempre Viva 123, Ciudad, País"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sistemas');
    }
};
