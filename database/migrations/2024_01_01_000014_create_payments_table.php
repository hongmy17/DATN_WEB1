<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->comment('Mã đơn hàng');
            $table->enum('payment_gateway', ['VNPay', 'Momo', 'ZaloPay', 'PayOS', 'COD'])
                ->comment('Cổng thanh toán');
            $table->string('transaction_code', 100)->nullable()->unique()->comment('Mã giao dịch từ cổng thanh toán');
            $table->decimal('amount', 12, 2)->comment('Số tiền giao dịch');
            $table->tinyInteger('status')->default(0)
                ->comment('0=chờ, 1=thành công, 2=thất bại, 3=hoàn tiền');
            $table->json('gateway_response')->nullable()->comment('Phản hồi đầy đủ từ cổng thanh toán');
            $table->dateTime('paid_at')->nullable()->comment('Thời điểm thanh toán thành công');
            $table->timestamps();

            $table->foreign('order_id')
                ->references('id')->on('orders')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->index('order_id', 'idx_payments_order');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });
        Schema::dropIfExists('payments');
    }
};
