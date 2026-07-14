<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refund_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->comment('Đơn hàng yêu cầu hoàn tiền');
            $table->unsignedBigInteger('user_id')->comment('Khách hàng gửi yêu cầu');

            // ── Bước 1: Khách hàng tạo yêu cầu ──
            $table->string('reason', 100)->comment('Lý do: hàng lỗi, không đúng mô tả, chưa nhận được hàng, đổi ý...');
            $table->text('reason_detail')->nullable()->comment('Mô tả chi tiết thêm của khách');
            $table->json('evidence_images')->nullable()->comment('Ảnh/video bằng chứng khách tải lên (mảng đường dẫn)');

            // Thông tin nhận tiền — chỉ bắt buộc khi payment_method = cod/bank_transfer
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_account_holder', 100)->nullable();

            $table->decimal('refund_amount', 12, 2)->comment('Số tiền cần hoàn — mặc định = total_amount của đơn');

            // ── Trạng thái xử lý ──
            // 0=pending (Chờ xử lý/Đang kiểm tra), 1=approved (Đã duyệt-chờ chuyển tiền),
            // 2=refunded (Đã hoàn tiền), 3=rejected (Đã từ chối)
            $table->tinyInteger('status')->default(0)->comment('0=pending,1=approved,2=refunded,3=rejected');

            // ── Bước 2 & 4: Admin xử lý ──
            $table->text('reject_reason')->nullable()->comment('Lý do từ chối do admin nhập');
            $table->string('receipt_image')->nullable()->comment('Ảnh biên lai chuyển tiền admin tải lên (Bước 4)');
            $table->unsignedBigInteger('reviewed_by')->nullable()->comment('Admin nào xử lý (users.id)');
            $table->dateTime('reviewed_at')->nullable();
            $table->dateTime('refunded_at')->nullable();

            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');

            $table->index('order_id', 'idx_refunds_order');
            $table->unique('order_id', 'uq_refunds_order'); // 1 đơn chỉ có 1 yêu cầu hoàn tiền tại 1 thời điểm — giải thích bên dưới
        });
    }

    public function down(): void
    {
        Schema::table('refund_requests', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['reviewed_by']);
        });
        Schema::dropIfExists('refund_requests');
    }
};