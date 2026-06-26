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
        Schema::table('product_images', function (Blueprint $table) {
            $table->foreignId('attribute_value_id')
                ->nullable()
                ->after('sort_order')
                ->comment('Ảnh này gắn với giá trị thuộc tính nào (vd: Đen, Đỏ...). NULL = ảnh chung')
                ->constrained('attribute_values')
                ->nullOnDelete();

            $table->index('attribute_value_id', 'idx_product_images_attribute_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->dropForeign(['attribute_value_id']);
            $table->dropIndex('idx_product_images_attribute_value');
            $table->dropColumn('attribute_value_id');
        });
    }
};
