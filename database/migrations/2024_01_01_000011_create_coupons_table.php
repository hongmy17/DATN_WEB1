<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('coupon_code', 50)->unique()->comment('Chuỗi mã giảm giá');
            $table->tinyInteger('type')->default(0)->comment('0=% giảm, 1=số tiền cố định');
            $table->decimal('value', 12, 2)->comment('Giá trị giảm');
            $table->decimal('min_order_value', 12, 2)->default(0)->comment('Giá trị đơn hàng tối thiểu');
            $table->integer('max_usage')->nullable()->comment('Số lần sử dụng tối đa (NULL=không giới hạn)');
            $table->integer('used_count')->default(0)->comment('Số lần đã sử dụng');
            $table->dateTime('start_date')->comment('Ngày bắt đầu hiệu lực');
            $table->dateTime('end_date')->comment('Ngày hết hiệu lực');
            $table->tinyInteger('status')->default(1)->comment('1=kích hoạt, 0=vô hiệu');
            $table->timestamps();

            $table->index('coupon_code', 'idx_coupons_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
