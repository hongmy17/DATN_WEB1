<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'variant_id',
        'product_name',
        'variant_description',
        'variant_sku',
        'product_thumbnail',
        'quantity',
        'unit_price',
        'compare_price',
        'total_price',
    ];

    // ─── Stock hooks ────────────────────────────────────────────
    // MỚI: tự trừ kho khi tạo OrderItem, tự hoàn kho khi xóa OrderItem.
    // Đặt ở Model (không phải Filament Resource) để logic này luôn
    // chạy đúng dù đơn hàng được tạo từ admin, từ trang khách, hay API.

    protected static function booted(): void
    {
        static::creating(function (OrderItem $item) {
            // Khóa row variant để tránh 2 đơn hàng cùng lúc trừ kho
            // dẫn tới âm kho (race condition khi nhiều khách checkout cùng lúc).
            $variant = ProductVariant::where('id', $item->variant_id)
                ->lockForUpdate()
                ->first();

            if (! $variant) {
                throw new \RuntimeException("Biến thể #{$item->variant_id} không tồn tại.");
            }

            // MỚI: nếu variant tắt quản lý kho (hàng đặt trước/dịch vụ)
            // → không kiểm tra và không trừ kho.
            if (! $variant->manage_stock) {
                return;
            }

            if ($variant->stock_quantity < $item->quantity) {
                throw new \RuntimeException(
                    "SKU {$variant->sku} không đủ hàng (còn {$variant->stock_quantity}, cần {$item->quantity})."
                );
            }

            $variant->decrement('stock_quantity', $item->quantity);
        });

        static::deleting(function (OrderItem $item) {
            // Khi xóa 1 item khỏi đơn (hủy 1 phần / sửa đơn) → hoàn lại kho
            // MỚI: bỏ qua nếu variant không quản lý kho
            $variant = ProductVariant::find($item->variant_id);
            if ($variant && ! $variant->manage_stock) {
                return;
            }

            ProductVariant::where('id', $item->variant_id)
                ->increment('stock_quantity', $item->quantity);
        });
    }

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    // Tính thành tiền
    public function subTotal(): float
    {
        return $this->quantity * $this->unit_price;
    }
}
