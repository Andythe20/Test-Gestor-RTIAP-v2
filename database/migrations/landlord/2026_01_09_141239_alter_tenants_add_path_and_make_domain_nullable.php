<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('landlord');

        $schema->table('tenants', function (Blueprint $table) {
            if (! Schema::connection('landlord')->hasColumn('tenants', 'path')) {
                $table->string('path')->unique()->after('name');
            }
        });

        $schema->table('tenants', function (Blueprint $table) {
            $table->string('domain')->nullable()->change();
        });
    }

    public function down(): void
    {
        $schema = Schema::connection('landlord');

        $schema->table('tenants', function (Blueprint $table) {
            if (Schema::connection('landlord')->hasColumn('tenants', 'path')) {
                $table->dropUnique(['path']);
                $table->dropColumn('path');
            }
        });

        $schema->table('tenants', function (Blueprint $table) {
            $table->string('domain')->nullable(false)->change();
        });
    }
};
