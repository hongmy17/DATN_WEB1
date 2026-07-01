<?php

namespace App\Mail;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * FIX/MỚI: Mailable gửi email xác nhận đơn hàng kèm hóa đơn PDF.
 *
 * implements ShouldQueue → Laravel TỰ ĐỘNG đẩy việc gửi mail này vào
 * bảng `jobs` thay vì gửi ngay lập tức trong request. Không cần gọi
 * Mail::queue() thủ công, chỉ cần Mail::to(...)->send() là Laravel
 * đã tự "queue" nó vì class implements interface này.
 *
 * Cần chạy `php artisan queue:work` (hoặc supervisor khi lên production)
 * để job thực sự được xử lý và email được gửi đi.
 */
class OrderConfirmationMail extends Mailable implements \Illuminate\Contracts\Queue\ShouldQueue
{
  use Queueable, SerializesModels;

  public Order $order;

  public function __construct(Order $order)
  {
    // FIX: eager-load quan hệ cần dùng trong view + PDF NGAY TẠI ĐÂY.
    // Vì Mailable bị serialize để lưu vào DB (cột `payload` bảng jobs),
    // nếu không load sẵn, lúc Queue Worker xử lý job (có thể vài giây
    // hoặc vài phút sau, ở 1 process hoàn toàn khác) các quan hệ sẽ
    // phải lazy-load lại — vẫn chạy được nhưng tốn thêm query.
    // Load sẵn ở đây giúp code rõ ràng và nhanh hơn.
    $this->order = $order->loadMissing(['items', 'user', 'coupon']);
  }

  /**
   * Tiêu đề + người gửi/nhận email.
   */
  public function envelope(): Envelope
  {
    $orderCode = 'NX-' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT);

    return new Envelope(
      from: new Address(
        config('mail.from.address'),
        config('mail.from.name')
      ),
      subject: "Xác nhận đơn hàng {$orderCode} — Nexus Store",
    );
  }

  /**
   * Nội dung email (file Blade sẽ tạo ở bước 3).
   */
  public function content(): Content
  {
    return new Content(
      view: 'emails.order-confirmation',
      with: [
        'order'     => $this->order,
        'orderCode' => 'NX-' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT),
      ],
    );
  }

  /**
   * File đính kèm — hóa đơn PDF được sinh ngay lúc gửi (không lưu file ra ổ đĩa).
   */
  public function attachments(): array
  {
    $orderCode = 'NX-' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT);

    $pdf = Pdf::loadView('pdf.invoice', ['order' => $this->order, 'orderCode' => $orderCode])
      ->setPaper('a4');

    return [
      \Illuminate\Mail\Mailables\Attachment::fromData(
        fn() => $pdf->output(),
        "HoaDon-{$orderCode}.pdf"
      )->withMime('application/pdf'),
    ];
  }
}