<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * FIX: bỏ cột attribute_id khỏi attribute_template_items.
 *
 * Lý do: Thông số kỹ thuật (AttributeTemplate) chỉ dùng để auto-fill THÔNG SỐ
 * KỸ THUẬT (tab "Thuộc tính riêng" của sản phẩm — text hiển thị như RAM/ROM).
 * Thuộc tính dùng để TẠO BIẾN THỂ (Màu sắc/Size) đã được quản lý riêng ở
 * trang "Thuộc tính" (AttributeResource) — không liên quan, không cần map.
 *
 * Cột attribute_id từng được thêm để link 2 luồng này lại, nhưng gây nhầm
 * lẫn UX (form Thông số kỹ thuật bắt chọn "Thuộc tính toàn cục" trong khi
 * admin chỉ muốn tạo mẫu thông số RAM/ROM cho danh mục iPhone).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attribute_template_items', function (Blueprint $table) {
            // Phải drop foreign key TRƯỚC rồi mới drop column được.
            // MySQL không cho phép xóa column đang được FK tham chiếu.
            $table->dropForeign('attribute_template_items_attribute_id_foreign');
            $table->dropColumn('attribute_id');
        });
    }

    public function down(): void
    {
        Schema::table('attribute_template_items', function (Blueprint $table) {
            $table->unsignedBigInteger('attribute_id')->nullable()
                ->comment('FK sang attributes - dùng để auto-fill ProductForm');

            // Thêm lại FK khi rollback
            $table->foreign('attribute_id')
                ->references('id')
                ->on('attributes')
                ->nullOnDelete();
        });
    }
};
