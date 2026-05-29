<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->comment('Mã sản phẩm');
            $table->string('image_url')->comment('Đường dẫn ảnh');
            $table->boolean('is_primary')->default(false)->comment('Đánh dấu ảnh chính');
            $table->integer('sort_order')->default(0)->comment('Thứ tự hiển thị');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('product_id')
                ->references('id')->on('products')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->index(['product_id', 'is_primary'], 'idx_product_images_product_primary');
        });
    }

    public function down(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });
        Schema::dropIfExists('product_images');
    }
};
