<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundRequest extends Model
{
    protected $fillable = [
        'order_id', 'user_id', 'reason', 'reason_detail', 'evidence_images',
        'bank_name', 'bank_account_number', 'bank_account_holder',
        'refund_amount', 'status', 'reject_reason', 'receipt_image',
        'reviewed_by', 'reviewed_at', 'refunded_at',
    ];

    protected $casts = [
        'evidence_images' => 'array',
        'refund_amount'   => 'float',
        'reviewed_at'     => 'datetime',
        'refunded_at'     => 'datetime',
    ];

    const STATUS_PENDING  = 0; // Chờ xử lý / Đang kiểm tra (2 label, 1 giá trị — xem đề xuất rút gọn)
    const STATUS_APPROVED = 1; // Đã duyệt - chờ chuyển tiền
    const STATUS_REFUNDED = 2; // Đã hoàn tiền
    const STATUS_REJECTED = 3; // Đã từ chối

    const REASONS = [
        'defective'    => 'Hàng lỗi',
        'not_as_desc'  => 'Không đúng mô tả',
        'not_received' => 'Chưa nhận được hàng',
        'changed_mind' => 'Đổi ý không muốn mua nữa',
        'other'        => 'Lý do khác',
    ];

    public function statusLabel(): string
    {
        return match ((int) $this->status) {
            self::STATUS_APPROVED => 'Đã duyệt - Chờ chuyển tiền',
            self::STATUS_REFUNDED => 'Đã hoàn tiền',
            self::STATUS_REJECTED => 'Đã từ chối',
            default                => 'Chờ xử lý',
        };
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}