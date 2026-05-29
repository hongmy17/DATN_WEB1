<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('Mã người dùng');
            $table->unsignedBigInteger('variant_id')->comment('Mã biến thể sản phẩm');
            $table->integer('quantity')->default(1)->comment('Số lượng');
            $table->timestamps();

            $table->unique(['user_id', 'variant_id'], 'uq_cart_user_variant');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('variant_id')
                ->references('id')->on('product_variants')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['variant_id']);
        });
        Schema::dropIfExists('cart_items');
    }
};
