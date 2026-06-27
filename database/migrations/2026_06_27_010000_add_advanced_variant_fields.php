<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) product_variants — bổ sung các trường còn thiếu so với WooCommerce/Flatsome
        Schema::table('product_variants', function (Blueprint $table) {
            // Biến thể mặc định — hiển thị ảnh/giá đại diện khi khách chưa chọn gì
            $table->boolean('is_default')->default(false)->after('status')
                ->comment('Biến thể được chọn sẵn khi khách vào trang sản phẩm');

            // Bật/tắt theo dõi tồn kho — sản phẩm dịch vụ/đặt trước có thể tắt
            $table->boolean('manage_stock')->default(true)->after('stock_quantity')
                ->comment('false = không trừ/kiểm tra kho khi đặt hàng (hàng đặt trước, dịch vụ)');

            // Mô tả ngắn riêng cho biến thể (vd: "Bản 512GB tặng thêm ốp")
            $table->text('description')->nullable()->after('image')
                ->comment('Mô tả ngắn riêng cho biến thể này');

            // Giá khuyến mãi có thời hạn — không cần tự tay đổi compare_price
            $table->decimal('sale_price', 12, 2)->nullable()->after('compare_price')
                ->comment('Giá khuyến mãi, chỉ áp dụng trong khoảng sale_starts_at - sale_ends_at');
            $table->timestamp('sale_starts_at')->nullable()->after('sale_price');
            $table->timestamp('sale_ends_at')->nullable()->after('sale_starts_at');

            // Gallery nhiều ảnh riêng cho biến thể (JSON array các đường dẫn ảnh)
            $table->json('gallery')->nullable()->after('image')
                ->comment('Danh sách nhiều ảnh phụ của riêng biến thể này (JSON array)');
        });

        // 2) Đảm bảo mỗi sản phẩm chỉ có ĐÚNG 1 biến thể is_default = true
        //    (ràng buộc logic này xử lý ở tầng ứng dụng / Model, DB chỉ lưu cờ)
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn([
                'is_default',
                'manage_stock',
                'description',
                'sale_price',
                'sale_starts_at',
                'sale_ends_at',
                'gallery',
            ]);
        });
    }
};
