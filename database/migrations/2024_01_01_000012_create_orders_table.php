<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('Mã người dùng đặt hàng');
            $table->unsignedBigInteger('coupon_id')->nullable()->comment('Mã giảm giá áp dụng');
            $table->unsignedBigInteger('address_id')->nullable()->comment('Mã địa chỉ giao hàng (snapshot)');
            $table->string('receiver_name', 100)->comment('Tên người nhận');
            $table->string('receiver_phone', 15)->comment('Số điện thoại người nhận');
            $table->text('shipping_address')->comment('Địa chỉ giao hàng (snapshot)');
            $table->tinyInteger('order_status')->default(0)
                ->comment('0=chờ xác nhận, 1=đang xử lý, 2=đang giao, 3=hoàn tất, 4=đã hủy');
            $table->decimal('subtotal', 12, 2)->comment('Tổng tiền hàng trước giảm giá');
            $table->decimal('discount_amount', 12, 2)->default(0)->comment('Số tiền được giảm');
            $table->decimal('total_amount', 12, 2)->comment('Tổng tiền thanh toán thực tế');
            $table->text('note')->nullable()->comment('Ghi chú đơn hàng');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('coupon_id')
                ->references('id')->on('coupons')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('address_id')
                ->references('id')->on('user_addresses')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->index(['user_id', 'order_status'], 'idx_orders_user_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['coupon_id']);
            $table->dropForeign(['address_id']);
        });
        Schema::dropIfExists('orders');
    }
};
