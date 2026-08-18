<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
        'stock_deducted',
        'coupon_code',
        'payment_method',
        'subtotal',
        'discount_amount',
        'total_amount',
        'note',
        'cancel_reason',
    ];

    protected $casts = [
        'stock_deducted'  => 'boolean',
        'subtotal'        => 'float',
        'discount_amount' => 'float',
        'total_amount'    => 'float',
    ];

    // ─── Trạng thái đơn hàng ────────────────────────────────────────────────
    const STATUS_PENDING          = 0;  // Chờ xác nhận (COD, hoặc VNPay đã thanh toán xong)
    const STATUS_CONFIRMED        = 1;  // Admin đã xác nhận → ĐÂY LÀ LÚC TRỪ KHO
    const STATUS_SHIPPING         = 2;
    const STATUS_COMPLETED        = 3;
    const STATUS_CANCELLED        = 4;
    const STATUS_AWAITING_PAYMENT = 5;  // Chờ thanh toán VNPay
    const STATUS_CANCEL_REQUESTED = 6;  // Khách yêu cầu hủy, chờ admin duyệt
    const STATUS_REFUNDED         = 7;  // Đã hoàn tiền (kết quả cuối của quy trình refund)

    const PAYMENT_TIMEOUT_MINUTES = 15;

    /**
     * NGUỒN SỰ THẬT DUY NHẤT cho tên trạng thái tiếng Việt.
     *
     * Trước đây danh sách này bị chép lại ở 5 nơi (model, bảng admin, bộ lọc,
     * widget biểu đồ, blade phía khách) và đã bắt đầu lệch nhau — ví dụ trạng
     * thái 5 có lúc là "Chờ thanh toán VNPay", có lúc "Chờ thanh toán (VNPay)".
     * Nay mọi nơi đều đọc từ hằng số này: sửa một chỗ, đổi toàn hệ thống (DRY).
     */
    const STATUS_LABELS = [
        self::STATUS_PENDING          => 'Chờ xác nhận',
        self::STATUS_CONFIRMED        => 'Đã xác nhận',
        self::STATUS_SHIPPING         => 'Đang giao',
        self::STATUS_COMPLETED        => 'Hoàn thành',
        self::STATUS_CANCELLED        => 'Đã hủy',
        self::STATUS_AWAITING_PAYMENT => 'Chờ thanh toán',
        self::STATUS_CANCEL_REQUESTED => 'Chờ xác nhận hủy',
        self::STATUS_REFUNDED         => 'Đã hoàn tiền',
    ];

    /**
     * MÁY TRẠNG THÁI (state machine): từ trạng thái hiện tại, admin được phép
     * chuyển sang những trạng thái nào.
     *
     * Trước đây admin có thể đổi tự do bằng dropdown — kể cả từ "Đã hủy" ngược
     * về "Đang giao", hoặc tự gán "Đã hoàn tiền" mà không qua quy trình hoàn
     * tiền. Bảng này chặn các bước đi vô nghĩa đó.
     *
     * Trạng thái 6 (Chờ xác nhận hủy) cố ý để trống: nó được xử lý bằng hai nút
     * riêng "Duyệt hủy" / "Từ chối hủy" trong OrdersTable, không phải bằng
     * dropdown đổi trạng thái.
     */
    const STATUS_TRANSITIONS = [
        self::STATUS_PENDING          => [self::STATUS_CONFIRMED, self::STATUS_CANCELLED],
        self::STATUS_CONFIRMED        => [self::STATUS_SHIPPING, self::STATUS_CANCELLED],
        self::STATUS_SHIPPING         => [self::STATUS_COMPLETED, self::STATUS_CANCELLED],
        self::STATUS_COMPLETED        => [],
        self::STATUS_CANCELLED        => [],
        self::STATUS_AWAITING_PAYMENT => [self::STATUS_CANCELLED],
        self::STATUS_CANCEL_REQUESTED => [],
        self::STATUS_REFUNDED         => [],
    ];

    /** Các trạng thái đã kết thúc — không được đổi tiếp sang đâu nữa. */
    const FINAL_STATUSES = [self::STATUS_CANCELLED, self::STATUS_REFUNDED, self::STATUS_COMPLETED];

    /**
     * Đơn đang "Chờ thanh toán" nhưng đã quá 15 phút kể từ lúc tạo mà người
     * dùng chưa quay lại (tắt trình duyệt, mất mạng, đóng tab...) → coi như
     * phiên thanh toán bên VNPay đã hết hạn, cần tạo phiên mới.
     */
    public function isPaymentExpired(): bool
    {
        return (int) $this->order_status === self::STATUS_AWAITING_PAYMENT
            && $this->created_at
            && $this->created_at->addMinutes(self::PAYMENT_TIMEOUT_MINUTES)->isPast();
    }

    /** Danh sách trạng thái admin được phép chuyển tới từ trạng thái hiện tại. */
    public function allowedNextStatuses(): array
    {
        $next = self::STATUS_TRANSITIONS[(int) $this->order_status] ?? [];

        return collect($next)
            ->mapWithKeys(fn($status) => [$status => self::STATUS_LABELS[$status]])
            ->all();
    }

    protected static function booted(): void
    {
        /*
        |----------------------------------------------------------------------
        | HOOK TỒN KHO — trái tim của yêu cầu "chỉ trừ kho khi admin xác nhận"
        |----------------------------------------------------------------------
        | Vòng đời tồn kho của một đơn hàng:
        |
        |   Khách đặt hàng (status 0/5) ──► KHÔNG trừ kho
        |            │                       (chỉ KIỂM TRA đủ hàng ở CheckoutController)
        |            ▼ admin xác nhận (status 1)
        |     TRỪ KHO  +  stock_deducted = true
        |            │
        |            ▼ hủy / hoàn tiền (status 4 hoặc 7)
        |     HOÀN KHO — nhưng CHỈ KHI stock_deducted đang là true
        |
        | Logic cũ nằm ở OrderItem::creating() (trừ kho ngay khi tạo dòng chi
        | tiết đơn) đã được gỡ bỏ — xem ghi chú trong app/Models/OrderItem.php.
        */
        static::updating(function (Order $order) {
            if (! $order->isDirty('order_status')) {
                return;
            }

            $new = (int) $order->order_status;
            $old = (int) $order->getOriginal('order_status');

            // ── TRỪ KHO: chuyển sang "Đã xác nhận" và chưa từng trừ ──────────
            if ($new === self::STATUS_CONFIRMED && ! $order->getOriginal('stock_deducted')) {

                DB::transaction(function () use ($order) {
                    foreach ($order->items as $item) {
                        // lockForUpdate() khóa dòng biến thể trong suốt transaction.
                        // Nếu hai admin cùng xác nhận hai đơn chứa cùng một sản
                        // phẩm, người thứ hai phải chờ người thứ nhất xong mới
                        // được đọc số tồn — nếu không, cả hai cùng đọc "còn 1"
                        // rồi cùng trừ và kho thành -1 (lỗi race condition).
                        // withTrashed(): biến thể có thể đã bị xóa mềm SAU khi
                        // khách đặt hàng. Nếu không có nó, truy vấn trả về null
                        // và đơn sẽ được xác nhận mà không trừ kho món đó.
                        $variant = ProductVariant::withTrashed()
                            ->where('id', $item->variant_id)
                            ->lockForUpdate()
                            ->first();

                        // Biến thể đã bị xóa, hoặc là hàng không quản lý kho
                        // (hàng đặt trước / dịch vụ) → bỏ qua.
                        if (! $variant || ! $variant->manage_stock) {
                            continue;
                        }

                        if ($variant->stock_quantity < $item->quantity) {
                            throw new \RuntimeException(
                                "Không đủ tồn kho cho \"{$item->product_name}\" "
                                    . "(còn {$variant->stock_quantity}, cần {$item->quantity}). "
                                    . 'Không thể xác nhận đơn hàng.'
                            );
                        }

                        $variant->decrement('stock_quantity', $item->quantity);
                    }
                });

                // Gán vào model, KHÔNG gọi save() riêng: Eloquent tính $dirty
                // SAU khi sự kiện updating chạy xong, nên giá trị này sẽ được
                // ghi cùng một câu UPDATE với order_status.
                $order->stock_deducted = true;

                return;
            }

            // ── HOÀN KHO: chuyển sang "Đã hủy" / "Đã hoàn tiền" ──────────────
            $isEnding  = in_array($new, [self::STATUS_CANCELLED, self::STATUS_REFUNDED], true);
            $wasEnding = in_array($old, [self::STATUS_CANCELLED, self::STATUS_REFUNDED], true);

            if ($isEnding && ! $wasEnding && $order->getOriginal('stock_deducted')) {

                DB::transaction(function () use ($order) {
                    foreach ($order->items as $item) {
                        // withTrashed() cũng cần ở đây: hàng của một biến thể đã
                        // ngừng bán vẫn phải được cộng trả về kho khi hủy đơn,
                        // nếu không số tồn sẽ bị hụt vĩnh viễn.
                        $variant = ProductVariant::withTrashed()->find($item->variant_id);

                        if (! $variant || ! $variant->manage_stock) {
                            continue;
                        }

                        $variant->increment('stock_quantity', $item->quantity);
                    }

                    // Trả lại lượt dùng mã giảm giá. CheckoutController tăng
                    // used_count ngay lúc đặt hàng; nếu đơn bị hủy mà không trả
                    // lại, mã giảm giá bị "ăn" mất một lượt oan.
                    if ($order->coupon_id) {
                        Coupon::where('id', $order->coupon_id)
                            ->where('used_count', '>', 0)
                            ->decrement('used_count');
                    }
                });

                $order->stock_deducted = false;
            }
        });

        /*
        |----------------------------------------------------------------------
        | HOOK ĐỒNG BỘ THANH TOÁN COD
        |----------------------------------------------------------------------
        | Đơn COD không đi qua cổng thanh toán nào nên trạng thái thu tiền phụ
        | thuộc hoàn toàn vào order_status do admin/shipper cập nhật.
        |
        | CHỈ áp dụng cho payment_method = 'cod'. Đơn VNPay có Payment do chính
        | cổng thanh toán / IPN cập nhật (xem PaymentController) — KHÔNG được
        | suy diễn từ order_status kẻo ghi đè sai kết quả thật của cổng.
        */
        static::updated(function (Order $order) {

            // Đơn đổi trạng thái → xóa cache số lượng đã bán để website tính lại.
            if ($order->wasChanged('order_status')) {
                Cache::forget('product_sold_counts_30d');
            }

            if ($order->payment_method !== 'cod') {
                return;
            }

            $payment = $order->payment;

            if (! $payment) {
                return;
            }

            $targetStatus = match ((int) $order->order_status) {
                self::STATUS_COMPLETED => Payment::STATUS_SUCCESS,
                self::STATUS_CANCELLED => Payment::STATUS_FAILED,
                self::STATUS_REFUNDED  => Payment::STATUS_REFUNDED,
                default                => Payment::STATUS_PENDING,
            };

            if ((int) $payment->status === $targetStatus) {
                return;
            }

            $payment->update([
                'status'  => $targetStatus,
                'paid_at' => $targetStatus === Payment::STATUS_SUCCESS ? now() : null,
            ]);
        });
    }

    public function statusLabel(): string
    {
        // Trạng thái 5 là trường hợp đặc biệt: cùng một giá trị nhưng hiển thị
        // khác nhau tùy đã quá 15 phút hay chưa.
        if ((int) $this->order_status === self::STATUS_AWAITING_PAYMENT && $this->isPaymentExpired()) {
            return 'Thanh toán quá hạn';
        }

        return self::STATUS_LABELS[(int) $this->order_status] ?? 'Không xác định';
    }

    // ─── Relationships ──────────────────────────────────────────────────────

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

    /**
     * Scope: chỉ những đơn CHẮC CHẮN đã có tiền thật ("doanh thu thực thu").
     *
     * - COD: chỉ tính khi đã "Hoàn thành" (giao xong, thu tiền tận tay).
     * - Thanh toán online: căn cứ vào bảng PAYMENTS chứ không phải order_status.
     *
     * VÌ SAO ĐỔI CÁCH TÍNH ONLINE?
     * Trước đây scope này tính doanh thu online từ order_status >= 1, dựa trên
     * giả định "VNPay thanh toán xong thì đơn tự nhảy sang Đã xác nhận". Nay
     * đơn VNPay đã thanh toán vẫn nằm ở "Chờ xác nhận" để admin duyệt (yêu cầu
     * của giảng viên) — nếu giữ cách cũ, toàn bộ đơn VNPay sẽ biến mất khỏi báo
     * cáo doanh thu.
     *
     * Cách mới tách bạch đúng trách nhiệm: "có tiền hay chưa" là câu hỏi của
     * bảng payments, không phải bảng orders. Nhờ vậy dù luồng trạng thái đơn
     * hàng có đổi thế nào, báo cáo doanh thu vẫn đúng.
     */
    public function scopeRevenue($query)
    {
        return $query->where(function ($q) {
            $q->where(function ($cod) {
                $cod->where('payment_method', 'cod')
                    ->where('order_status', self::STATUS_COMPLETED);
            })->orWhere(function ($online) {
                $online->where('payment_method', '!=', 'cod')
                    ->whereHas('payment', fn($p) => $p->where('status', Payment::STATUS_SUCCESS))
                    ->whereNotIn('order_status', [self::STATUS_REFUNDED, self::STATUS_CANCELLED]);
            });
        });
    }
}
