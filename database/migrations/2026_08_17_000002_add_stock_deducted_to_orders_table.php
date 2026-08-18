<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Thêm cờ stock_deducted vào bảng orders.
 *
 * VÌ SAO CẦN CỘT NÀY?
 * Từ nay tồn kho chỉ bị trừ khi admin xác nhận đơn (không phải lúc khách đặt).
 * Nghĩa là một đơn có thể bị hủy khi CHƯA từng bị trừ kho. Nếu không có cột
 * này, hook "hủy đơn thì hoàn kho" sẽ cộng khống hàng vào kho cho những đơn
 * chưa được xác nhận — kho tăng lên từ hư không.
 *
 * Cột này là "trí nhớ" của đơn hàng: đã trừ kho hay chưa. Nhờ nó, thao tác
 * trừ/hoàn kho luôn đúng một lần dù hook chạy bao nhiêu lần (idempotency).
 */
return new class extends Migration
{
  public function up(): void
  {
    Schema::table('orders', function (Blueprint $table) {
      $table->boolean('stock_deducted')
        ->default(false)
        ->after('order_status')
        ->comment('Đã trừ tồn kho hay chưa — chỉ trừ khi admin xác nhận đơn');
    });

    // ── Xử lý DỮ LIỆU CŨ ────────────────────────────────────────────────
    // Các đơn đã tồn tại trước bản cập nhật này đều bị trừ kho ngay lúc đặt
    // (theo logic cũ ở OrderItem::creating). Phải đánh dấu chúng là ĐÃ TRỪ,
    // nếu không khi admin hủy một đơn cũ, hàng sẽ không được hoàn về kho.
    //
    // Loại trừ:
    //   4 = Đã hủy      → logic cũ đã hoàn kho rồi, không đánh dấu nữa
    //   5 = Chờ thanh toán VNPay → đơn treo, coi như chưa chốt
    //   7 = Đã hoàn tiền → hàng đã trả về kho theo quy trình hoàn tiền
    DB::table('orders')
      ->whereNotIn('order_status', [4, 5, 7])
      ->update(['stock_deducted' => true]);
  }

  public function down(): void
  {
    Schema::table('orders', function (Blueprint $table) {
      $table->dropColumn('stock_deducted');
    });
  }
};
