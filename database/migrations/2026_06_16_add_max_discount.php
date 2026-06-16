<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Thêm cột max_discount vào bảng coupons.
 * KHÔNG sửa migration gốc (2024_01_01_000011_create_coupons_table.php).
 * Chạy: php artisan migrate
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->decimal('max_discount', 12, 2)
                ->nullable()
                ->after('value')
                ->comment('Số tiền giảm tối đa khi type=0 (%). NULL = không giới hạn.');
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn('max_discount');
        });
    }
};