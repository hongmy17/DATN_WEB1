<?php
// Giữ nguyên file cũ — coupon không liên quan đến loại sản phẩm

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('coupons')->insert([
            [
                'coupon_code' => 'WELCOME10', 'type' => 0, 'value' => 10.00,
                'min_order_value' => 200000, 'max_usage' => 500, 'used_count' => 42,
                'start_date' => '2024-01-01', 'end_date' => '2026-12-31', 'status' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'coupon_code' => 'SALE50K', 'type' => 1, 'value' => 50000.00,
                'min_order_value' => 500000, 'max_usage' => 200, 'used_count' => 18,
                'start_date' => '2024-06-01', 'end_date' => '2024-06-30', 'status' => 0,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'coupon_code' => 'SUMMER25', 'type' => 0, 'value' => 25.00,
                'min_order_value' => 1000000, 'max_usage' => null, 'used_count' => 0,
                'start_date' => '2024-07-01', 'end_date' => '2026-12-31', 'status' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'coupon_code' => 'FREESHIP', 'type' => 1, 'value' => 30000.00,
                'min_order_value' => 300000, 'max_usage' => 1000, 'used_count' => 155,
                'start_date' => '2024-01-01', 'end_date' => '2026-12-31', 'status' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ],
        ]);
    }
}
