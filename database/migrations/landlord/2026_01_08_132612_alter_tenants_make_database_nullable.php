<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::connection('landlord')->statement("
            ALTER TABLE `tenants`
            MODIFY database VARCHAR(255) NULL
        ");
    }

    public function down(): void
    {
        DB::connection('landlord')->statement("
            ALTER TABLE `tenants`
            MODIFY database VARCHAR(255) NOT NULL
        ");
    }
};
