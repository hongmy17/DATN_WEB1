<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tính năng 2: Attribute riêng cho từng sản phẩm (không dùng chung toàn hệ thống)
 * Giống WooCommerce "Add custom product attribute"
 *
 * product_custom_attributes — lưu tên attribute riêng của sản phẩm
 * product_custom_attribute_values — lưu các giá trị của attribute đó
 */
return new class extends Migration
{
    public function up(): void
    {
        // Attribute riêng (VD: "Chất liệu", "Xuất xứ")
        Schema::create('product_custom_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name');                          // Tên attribute
            $table->boolean('is_visible')->default(true);   // Hiện trên trang SP
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Giá trị của attribute riêng (VD: "Cotton", "Việt Nam")
        Schema::create('product_custom_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_custom_attribute_id');
            $table->string('value');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Đặt tên constraint ngắn thủ công, tránh vượt 64 ký tự của MySQL
            $table->foreign('product_custom_attribute_id', 'pcav_custom_attr_id_fk')
                ->references('id')
                ->on('product_custom_attributes')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_custom_attribute_values');
        Schema::dropIfExists('product_custom_attributes');
    }
};
