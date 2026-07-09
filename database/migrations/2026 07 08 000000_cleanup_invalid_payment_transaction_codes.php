<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * FIX: bug cũ trong PaymentController::handleFailed() từng lưu thẳng
     * vnp_TransactionNo = "0" (giá trị VNPay trả về khi thanh toán bị hủy/thất
     * bại, KHÔNG phải mã giao dịch thật) vào cột payments.transaction_code —
     * cột này có ràng buộc unique, nên các dòng "0" cũ để lại sẽ làm những lần
     * hủy thanh toán VNPay tiếp theo bị lỗi:
     *   "Duplicate entry '0' for key 'payments.payments_transaction_code_unique'"
     *
     * Code đã được sửa để không còn lưu "0" nữa (xem PaymentController),
     * nhưng migration này dọn luôn các dòng "0" đã lỡ lưu từ trước, để mọi máy
     * (dev, staging, production) chỉ cần chạy `php artisan migrate` một lần là
     * xong, không cần chạy tay câu UPDATE nào cả.
     */
    public function up(): void
    {
        DB::table('payments')
            ->where('transaction_code', '0')
            ->update(['transaction_code' => null]);
    }

    public function down(): void
    {
        // Không cần rollback: đây là data-cleanup, không phải thay đổi schema.
    }
};