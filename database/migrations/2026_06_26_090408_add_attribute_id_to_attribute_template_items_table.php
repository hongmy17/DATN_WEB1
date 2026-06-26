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
        Schema::table('attribute_template_items', function (Blueprint $table) {
            $table->foreignId('attribute_id')
                ->nullable()
                ->after('name')
                ->comment('FK sang attributes - dùng để auto-fill ProductForm')
                ->constrained('attributes')
                ->nullOnDelete();

            $table->index('attribute_id', 'idx_ati_attribute');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attribute_template_items', function (Blueprint $table) {
            $table->dropForeign(['attribute_id']);
            $table->dropIndex('idx_ati_attribute');
            $table->dropColumn('attribute_id');
        });
    }
};
