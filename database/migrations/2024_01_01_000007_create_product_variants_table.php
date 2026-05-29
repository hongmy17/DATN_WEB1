<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->comment('Mã sản phẩm');
            $table->string('sku', 100)->unique()->comment('Mã SKU biến thể');
            $table->decimal('price', 12, 2)->comment('Giá bán');
            $table->decimal('compare_price', 12, 2)->nullable()->comment('Giá so sánh (giá gốc)');
            $table->integer('stock_quantity')->default(0)->comment('Số lượng tồn kho');
            $table->string('image')->nullable()->comment('Ảnh riêng của biến thể');
            $table->tinyInteger('status')->default(1)->comment('1=hoạt động, 0=ẩn');
            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')->on('products')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->index('product_id', 'idx_product_variants_product');
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });
        Schema::dropIfExists('product_variants');
    }
};
