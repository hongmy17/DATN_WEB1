<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('name')->nullable()->after('code');
            });
        }

        if (Schema::hasColumn('users', 'full_name')) {
            DB::statement('UPDATE users SET name = full_name WHERE name IS NULL OR name = ""');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'name') && ! Schema::hasColumn('users', 'full_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('name', 'full_name');
            });
        }
    }
};
