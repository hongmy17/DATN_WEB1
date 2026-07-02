<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // SKU biến thể tại thời điểm đặt — quan trọng cho đổi trả
            $table->string('variant_sku', 100)
                ->nullable()
                ->after('variant_description')
                ->comment('SKU biến thể (snapshot)');

            // Ảnh sản phẩm tại thời điểm đặt — dùng cho email và invoice PDF
            $table->string('product_thumbnail', 255)
                ->nullable()
                ->after('variant_sku')
                ->comment('Đường dẫn ảnh sản phẩm (snapshot)');

            // Giá gốc tại thời điểm đặt — hiển thị "đã tiết kiệm X₫"
            $table->decimal('compare_price', 12, 2)
                ->nullable()
                ->after('unit_price')
                ->comment('Giá gốc tại thời điểm đặt (snapshot)');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['variant_sku', 'product_thumbnail', 'compare_price']);
        });
    }
};
