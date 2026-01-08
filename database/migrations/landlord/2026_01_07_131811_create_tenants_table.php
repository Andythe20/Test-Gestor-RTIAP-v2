<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('landlord')->create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('domain')->unique();

            // provisioning|active|failed (tú ya usas active)
            $table->string('status')->default('provisioning');

            // nombre de la DB del tenant (tenant1, tenant2, ...)
            $table->string('database')->nullable()->unique();

            // usuario MySQL propio del tenant (tenant1_app, tenant2_app, ...)
            $table->string('db_username')->nullable()->unique();

            // password cifrada con encrypt() (texto largo)
            $table->text('db_password_encrypted')->nullable();

            $table->timestamps();
        });
    }
    // Correr migrate:fresh !!

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('tenants');
    }
};
