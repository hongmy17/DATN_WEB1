<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'address_id',
        'coupon_id',
        'receiver_name',
        'receiver_phone',
        'province',
        'district',
        'ward',
        'address_detail',
        'shipping_address',
        'order_status',
        'coupon_code',
        'payment_method',
        'subtotal',
        'discount_amount',
        'total_amount',
        'note',
        'cancel_reason',
    ];

    // Trạng thái đơn hàng
    const STATUS_PENDING          = 0;  // Chờ xác nhận (COD hoặc VNPay đã thanh toán xong)
    const STATUS_CONFIRMED        = 1;
    const STATUS_SHIPPING         = 2;
    const STATUS_COMPLETED        = 3;
    const STATUS_CANCELLED        = 4;
    const STATUS_AWAITING_PAYMENT = 5;  // Chờ thanh toán VNPay
    const STATUS_CANCEL_REQUESTED = 6;  // Khách yêu cầu hủy, chờ admin xác nhận
    const STATUS_REFUNDED = 7; // Đã hoàn tiền (kết quả cuối của quy trình refund)
    const PAYMENT_TIMEOUT_MINUTES = 15;

    /**
     * Đơn đang "Chờ thanh toán" nhưng đã quá 15 phút kể từ lúc tạo mà
     * người dùng chưa quay lại xác nhận (bị gián đoạn: tắt trình duyệt,
     * mất mạng, đóng tab giữa chừng...) → coi như phiên thanh toán cũ
     * đã hết hạn bên phía VNPay, cần tạo phiên thanh toán mới.
     */
    public function isPaymentExpired(): bool
    {
        return (int) $this->order_status === self::STATUS_AWAITING_PAYMENT
            && $this->created_at
            && $this->created_at->addMinutes(self::PAYMENT_TIMEOUT_MINUTES)->isPast();
    }

    // ─── Stock hook: hoàn kho khi đơn chuyển sang "Đã hủy" ───────
    // MỚI: chỉ hoàn kho 1 LẦN tại đúng thời điểm order_status chuyển
    // SANG STATUS_CANCELLED từ 1 trạng thái khác (không lặp lại nếu
    // record được save nhiều lần ở trạng thái hủy, và không hoàn kho
    // khi đơn được TẠO MỚI thẳng với status = hủy, vì khi đó chưa
    // từng bị trừ kho lúc nào cả — chỉ trừ kho lúc tạo OrderItem).
    protected static function booted(): void
    {
        static::updating(function (Order $order) {
            $isBecomingCancelled = $order->isDirty('order_status')
                && (int) $order->order_status === self::STATUS_CANCELLED
                && (int) $order->getOriginal('order_status') !== self::STATUS_CANCELLED;

            if (! $isBecomingCancelled) {
                return;
            }

            $order->items()->with('variant')->get()->each(function (OrderItem $item) {
                ProductVariant::where('id', $item->variant_id)
                    ->increment('stock_quantity', $item->quantity);
            });
        });

        // ─── COD hook: đồng bộ Payment status theo Order status ──────
        // Đơn COD không đi qua cổng thanh toán nào nên trạng thái thu
        // tiền hoàn toàn phụ thuộc vào order_status do admin/shipper
        // cập nhật thủ công. Mỗi khi order_status đổi, Payment tương
        // ứng sẽ tự động đồng bộ theo bảng ánh xạ bên dưới — kể cả khi
        // đổi XUÔI (Chờ xác nhận → Hoàn thành) lẫn đổi NGƯỢC (Hoàn
        // thành → Đang giao, do sửa nhầm hoặc test lại).
        //
        // CHỈ áp dụng cho đơn payment_method = 'cod'. Đơn vnpay/
        // bank_transfer có Payment do chính cổng thanh toán / IPN cập
        // nhật (xem PaymentController), KHÔNG được suy diễn từ
        // order_status kẻo ghi đè sai kết quả thật của cổng.
        static::updated(function (Order $order) {
            if (! $order->wasChanged('order_status') || $order->payment_method !== 'cod') {
                return;
            }

            $payment = $order->payment;
            if (! $payment) {
                return;
            }

            $targetStatus = match ((int) $order->order_status) {
                self::STATUS_COMPLETED => Payment::STATUS_SUCCESS,
                self::STATUS_CANCELLED => Payment::STATUS_FAILED,
                default                => Payment::STATUS_PENDING, // Chờ xác nhận / Đã xác nhận / Đang giao / Chờ xác nhận hủy
            };

            if ((int) $payment->status === $targetStatus) {
                return; // đã đúng trạng thái rồi, khỏi update tránh vòng lặp thừa
            }

            $payment->update([
                'status'  => $targetStatus,
                'paid_at' => $targetStatus === Payment::STATUS_SUCCESS ? now() : null,
            ]);
        });
    }

    public function statusLabel(): string
    {
        return match ((int) $this->order_status) {
            0 => 'Chờ xác nhận',
            1 => 'Đã xác nhận',
            2 => 'Đang giao',
            3 => 'Hoàn thành',
            4 => 'Đã hủy',
            5 => $this->isPaymentExpired()
                ? 'Thanh toán quá hạn'
                : 'Chờ thanh toán',
            6 => 'Chờ xác nhận hủy',
            7 => 'Đã hoàn tiền',
            default => 'Không xác định',
        };
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(UserAddress::class, 'address_id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function refundRequest()
{
    return $this->hasOne(RefundRequest::class);
}
}