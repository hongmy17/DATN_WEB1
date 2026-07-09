<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * FIX: cột này chưa từng được tạo ở bất kỳ migration nào trước đó.
     * Vì Order::$fillable cũng không khai báo 'payment_method', Eloquent
     * âm thầm bỏ qua field này khi Order::create(...) — không báo lỗi,
     * chỉ đơn giản là không lưu. Hệ quả: $order->payment_method luôn là
     * null, nên điều kiện `if ($order->payment_method === 'vnpay')` trong
     * CheckoutController không bao giờ đúng, và luồng VNPay không bao giờ
     * được kích hoạt dù người dùng có chọn VNPay hay không.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method', 20)
                ->default('cod')
                ->comment('Phương thức thanh toán: cod, vnpay, momo, bank, zalopay');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
    }
};