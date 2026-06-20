<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bổ sung cột để hỗ trợ logic admin chặt chẽ hơn, đúng chuẩn Flasome:
 * - base_sku: SKU gốc của sản phẩm, để variant kế thừa nếu admin không nhập riêng
 * - base_price: giá tham khảo hiển thị ở list khi sản phẩm CHƯA có variant nào
 *   (Flasome dùng giá min/max của variant, nhưng cần fallback khi chưa generate)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('base_sku', 50)->nullable()->after('code')
                ->comment('SKU gốc, variant sẽ kế thừa nếu để trống');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('base_sku');
        });
    }
};
