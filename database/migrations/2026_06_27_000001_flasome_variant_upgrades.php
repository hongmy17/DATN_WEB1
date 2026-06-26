<?php

/**
 * Migration: 2026_06_27_000001_flasome_variant_upgrades.php
 *
 * Đặt file này vào: database/migrations/
 *
 * Mục đích: Fix các SKU biến thể sinh ra sai thứ tự thuộc tính.
 * VD: USER000009-512GB-SILVER → USER000009-SILVER-512GB
 *     (Màu sắc attribute_id=1 phải đứng trước Dung lượng attribute_id=2)
 *
 * QUAN TRỌNG: Chạy php artisan migrate trước khi deploy code mới.
 * Migration này chỉ sửa SKU — không xóa/tạo variant.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Lấy tất cả variants có attributeValues
        $variants = DB::table('product_variants as pv')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->select('pv.id', 'pv.sku', 'p.base_sku', 'p.code')
            ->get();

        foreach ($variants as $variant) {
            // Lấy attribute values của variant, sort đúng thứ tự
            $values = DB::table('variant_attribute_values as vav')
                ->join('attribute_values as av', 'av.id', '=', 'vav.attribute_value_id')
                ->where('vav.variant_id', $variant->id)
                ->orderBy('av.attribute_id')   // Màu sắc (1) trước Dung lượng (2)
                ->orderBy('av.sort_order')
                ->pluck('av.value');

            if ($values->isEmpty()) {
                continue;
            }

            $skuPrefix = $variant->base_sku
                ? strtoupper(Str::slug($variant->base_sku))
                : strtoupper(Str::slug($variant->code));

            $valueSlug  = $values->map(fn ($v) => Str::slug($v))->implode('-');
            $correctSku = strtoupper($skuPrefix . '-' . $valueSlug);

            if (strtoupper($variant->sku) !== $correctSku) {
                // Kiểm tra SKU mới chưa bị trùng
                $exists = DB::table('product_variants')
                    ->where('sku', $correctSku)
                    ->where('id', '!=', $variant->id)
                    ->exists();

                if (! $exists) {
                    // Cập nhật order_items trước để giữ tham chiếu
                    // (order_items dùng variant_id FK, không dùng SKU — an toàn)
                    DB::table('product_variants')
                        ->where('id', $variant->id)
                        ->update(['sku' => $correctSku]);
                }
                // Nếu trùng thì giữ nguyên SKU cũ, admin tự sửa tay
            }
        }
    }

    public function down(): void
    {
        // Không thể rollback vì SKU cũ không được lưu lại
        // Nếu cần, restore từ backup DB
    }
};
