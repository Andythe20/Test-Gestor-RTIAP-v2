<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illumunate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('landlord');

        $schema->table('tenant', function (Blueprint $table) {
            if (!Schema::connection('landlord')->hasColumn('tenants', 'path')) {
                $table->string('path')->nullable()->unique()->after('status');
            }
        });
    }

    public function down(): void
    {
        $schema = Schema::connection('landlord');
        if ($schema->hasColumn('tenants', 'path')) {
            $schema->table('tenants', function (Blueprint $table) {
                $table->dropUnique(['path']);
                $table->dropColumn('path');
            });
        }
    }
};
