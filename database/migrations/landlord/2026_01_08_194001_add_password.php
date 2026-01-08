<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('landlord');

        if (! $schema->hasTable('users')) {
            return;
        }

        if ($schema->hasColumn('users', 'password')) {
            return;
        }

        $schema->table('users', function (Blueprint $table) {
            // nullable para no romper usuarios existentes sin password
            $table->string('password')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        $schema = Schema::connection('landlord');

        if (! $schema->hasTable('users')) {
            return;
        }

        if (! $schema->hasColumn('users', 'password')) {
            return;
        }

        $schema->table('users', function (Blueprint $table) {
            $table->dropColumn('password');
        });
    }
};
