<?php

namespace App\Mail;

use App\Models\RefundRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RefundStatusMail extends Mailable implements \Illuminate\Contracts\Queue\ShouldQueue
{
    use Queueable, SerializesModels;

    public RefundRequest $refund;

    public int $tries = 3;     // FIX: thử lại tối đa 3 lần nếu gửi thất bại
    public int $backoff = 10;    // FIX: đợi 10 giây giữa mỗi lần thử lại (để kết nối cũ chắc chắn được dọn sạch)
    public function __construct(RefundRequest $refund)
    {
        $this->refund = $refund->loadMissing(['order', 'user']);
    }

    public function envelope(): Envelope
    {
        $orderCode = 'NX-' . str_pad($this->refund->order_id, 6, '0', STR_PAD_LEFT);

        $subject = match ((int) $this->refund->status) {
            \App\Models\RefundRequest::STATUS_REFUNDED => "Đã hoàn tiền đơn hàng {$orderCode}",
            \App\Models\RefundRequest::STATUS_REJECTED => "Yêu cầu hoàn tiền {$orderCode} bị từ chối",
            default                                     => "Cập nhật yêu cầu hoàn tiền {$orderCode}",
        };

        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.refund-status',
            with: [
                'refund'    => $this->refund,
                'orderCode' => 'NX-' . str_pad($this->refund->order_id, 6, '0', STR_PAD_LEFT),
            ],
        );
    }
}