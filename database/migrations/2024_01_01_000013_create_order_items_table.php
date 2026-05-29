<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->comment('Mã đơn hàng');
            $table->unsignedBigInteger('variant_id')->comment('Mã biến thể sản phẩm');
            $table->string('product_name')->comment('Tên sản phẩm (snapshot)');
            $table->string('variant_description')->nullable()->comment('Mô tả biến thể (snapshot)');
            $table->integer('quantity')->comment('Số lượng');
            $table->decimal('unit_price', 12, 2)->comment('Đơn giá tại thời điểm đặt');
            $table->decimal('total_price', 12, 2)->comment('Thành tiền');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('order_id')
                ->references('id')->on('orders')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('variant_id')
                ->references('id')->on('product_variants')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['variant_id']);
        });
        Schema::dropIfExists('order_items');
    }
};
