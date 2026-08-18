<?php

namespace App\Console\Commands;

use App\Mail\RefundStatusMail;
use App\Models\RefundRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Tự động TỪ CHỐI các yêu cầu hoàn tiền mà admin để quá hạn không xử lý.
 *
 * Lý do nghiệp vụ: yêu cầu hoàn tiền treo vô thời hạn khiến khách hàng không
 * biết trạng thái đơn của mình, còn đơn hàng thì kẹt ở trạng thái trung gian.
 * Đặt một hạn chót cứng (3 ngày) buộc hệ thống luôn đi tới một kết luận.
 *
 * Chạy tay:      php artisan refunds:auto-reject
 * Chạy thử khô:  php artisan refunds:auto-reject --dry-run
 * Chạy tự động:  đã đăng ký trong routes/console.php (mỗi giờ 1 lần)
 */
class AutoRejectStaleRefunds extends Command
{
  protected $signature = 'refunds:auto-reject
                            {--dry-run : Chỉ liệt kê, không thay đổi dữ liệu}';

  protected $description = 'Tự động từ chối yêu cầu hoàn tiền quá 3 ngày admin chưa xác nhận';

  /** Số ngày tối đa admin được phép để yêu cầu ở trạng thái "Chờ xử lý". */
  public const DEADLINE_DAYS = 3;

  public function handle(): int
  {
    $isDryRun = (bool) $this->option('dry-run');

    // Mốc thời gian: 3 ngày TRƯỚC thời điểm hiện tại.
    // Yêu cầu nào có created_at <= mốc này tức là đã tồn tại quá 3 ngày.
    $deadline = now()->subDays(self::DEADLINE_DAYS);

    $staleRequests = RefundRequest::query()
      ->where('status', RefundRequest::STATUS_PENDING)
      ->where('created_at', '<=', $deadline)
      ->with(['user', 'order'])
      ->get();

    if ($staleRequests->isEmpty()) {
      $this->info('Không có yêu cầu hoàn tiền nào quá hạn.');
      return self::SUCCESS;
    }

    $this->info("Tìm thấy {$staleRequests->count()} yêu cầu quá hạn " . self::DEADLINE_DAYS . ' ngày.');

    foreach ($staleRequests as $refund) {
      $orderCode = 'NX-' . str_pad($refund->order_id, 6, '0', STR_PAD_LEFT);

      if ($isDryRun) {
        $this->line("  [DRY-RUN] Sẽ từ chối yêu cầu #{$refund->id} — đơn {$orderCode}");
        continue;
      }

      $refund->update([
        'status'        => RefundRequest::STATUS_REJECTED,
        'reject_reason' => 'Yêu cầu bị hệ thống tự động từ chối do quá '
          . self::DEADLINE_DAYS . ' ngày chưa được xử lý. '
          . 'Vui lòng liên hệ bộ phận chăm sóc khách hàng nếu bạn vẫn cần hỗ trợ.',
        'reviewed_at'   => now(),
        // reviewed_by = null nghĩa là "hệ thống tự xử lý", không phải admin nào.
        // Cột này nullable (xem migration create_refund_requests_table).
        'reviewed_by'   => null,
      ]);

      // Gửi email thông báo cho khách. RefundStatusMail tự chọn tiêu đề
      // "Yêu cầu hoàn tiền ... bị từ chối" dựa trên status vừa cập nhật.
      // Mail này implements ShouldQueue nên cần `php artisan queue:work`.
      if ($refund->user?->email) {
        try {
          Mail::to($refund->user->email)->send(new RefundStatusMail($refund));
        } catch (\Throwable $e) {
          // Gửi mail hỏng KHÔNG được làm hỏng cả tiến trình — trạng thái
          // đã cập nhật xong, chỉ ghi log để xử lý sau.
          Log::error("[refunds:auto-reject] Gửi mail thất bại cho yêu cầu #{$refund->id}: " . $e->getMessage());
        }
      }

      $this->line("  Đã tự động từ chối yêu cầu #{$refund->id} — đơn {$orderCode}");
    }

    if (! $isDryRun) {
      Log::info('[refunds:auto-reject] Đã xử lý ' . $staleRequests->count() . ' yêu cầu quá hạn.');
    }

    $this->info('Hoàn tất.');

    return self::SUCCESS;
  }
}
