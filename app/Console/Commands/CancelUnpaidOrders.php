<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Tự động HỦY các đơn VNPay khách bỏ dở (không quay lại thanh toán).
 *
 * Model Order đã có sẵn hằng số PAYMENT_TIMEOUT_MINUTES = 15 và hàm
 * isPaymentExpired(), nhưng trước đây không có tiến trình nào dọn dẹp, nên
 * đơn "Chờ thanh toán" tồn tại vĩnh viễn và làm nhiễu thống kê.
 *
 * Lưu ý: các đơn này CHƯA từng bị trừ kho (kho chỉ trừ khi admin xác nhận),
 * nên hủy chúng hoàn toàn an toàn — hook trong Order::booted() sẽ tự bỏ qua
 * bước hoàn kho nhờ cờ stock_deducted = false.
 *
 * Chạy tay: php artisan orders:cancel-unpaid
 */
class CancelUnpaidOrders extends Command
{
  protected $signature   = 'orders:cancel-unpaid';
  protected $description = 'Tự động hủy đơn VNPay quá hạn thanh toán';

  public function handle(): int
  {
    $deadline = now()->subMinutes(Order::PAYMENT_TIMEOUT_MINUTES);

    $expiredOrders = Order::query()
      ->where('order_status', Order::STATUS_AWAITING_PAYMENT)
      ->where('created_at', '<=', $deadline)
      ->get();

    if ($expiredOrders->isEmpty()) {
      $this->info('Không có đơn nào quá hạn thanh toán.');
      return self::SUCCESS;
    }

    foreach ($expiredOrders as $order) {
      $order->update([
        'order_status'  => Order::STATUS_CANCELLED,
        'cancel_reason' => 'Quá hạn thanh toán ' . Order::PAYMENT_TIMEOUT_MINUTES . ' phút.',
      ]);

      $order->payment?->update(['status' => Payment::STATUS_FAILED]);

      $this->line('  Đã hủy đơn NX-' . str_pad($order->id, 6, '0', STR_PAD_LEFT));
    }

    Log::info('[orders:cancel-unpaid] Đã hủy ' . $expiredOrders->count() . ' đơn quá hạn thanh toán.');
    $this->info("Hoàn tất: {$expiredOrders->count()} đơn.");

    return self::SUCCESS;
  }
}
