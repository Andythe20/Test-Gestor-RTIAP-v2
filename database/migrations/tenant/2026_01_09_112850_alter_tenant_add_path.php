<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('landlord');

        if (! $schema->hasColumn('tenants', 'path')) {
            $schema->table('tenants', function (Blueprint $table) {
                $table->string('path')->nullable()->unique()->after('name');
            });
        }

        DB::connection('landlord')->statement(
            'ALTER TABLE tenants MODIFY domain VARCHAR(255) NULL'
        );

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

        DB::connection('landlord')->statement(
            'ALTER TABLE tenants MODIFY domain VARCHAR(255) NOT NULL'
        );
    }
};
