<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('landlord');

        $hasRole = $schema->hasColumn('users', 'role');
        $hasTenantid = $schema->hasColumn('users', 'tenant_id');

        if ($hasRole && $hasTenantid) {
            return;
        }

        $schema->table('users', function (Blueprint $table) use ($hasRole, $hasTenantid) {
            if (! $hasRole) {
                $table->string('role')->default('tenant')->after('password');
            }
            if (! $hasTenantid) {
                $table->unsignedBigInteger('tenant_id')->nullable()->after('role');
            }

        });

    }

    public function down(): void
    {
        Schema::connection('landlord')->table('users', function (Blueprint $table) {
            if (Schema::connection('landlord')->hasColumn('users', 'tenant_id')) {
                $table->dropIndex(['tenant_id']);
                $table->dropColumn('tenant_id');
            }
            if (Schema::connection('landlord')->hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
