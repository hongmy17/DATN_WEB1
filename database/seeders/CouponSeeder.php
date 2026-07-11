<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('coupons')->truncate();
        $now = now();

        DB::table('coupons')->insert([
            [
                // Mã chào mừng — giảm 10% cho khách đặt đơn đầu tiên
                'coupon_code'     => 'WELCOME10',
                'type'            => 0,           // 0 = % giảm
                'value'           => 10.00,       // giảm 10%
                'max_discount'    => 200000,       // giảm tối đa 200K
                'min_order_value' => 200000,       // đơn tối thiểu 200K
                'max_usage'       => 1000,
                'used_count'      => 58,
                'start_date'      => '2024-01-01',
                'end_date'        => '2026-12-31',
                'status'          => 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                // Flash sale — giảm 25% dịp hè
                'coupon_code'     => 'SUMMER25',
                'type'            => 0,
                'value'           => 25.00,
                'max_discount'    => 500000,
                'min_order_value' => 1000000,
                'max_usage'       => null,         // không giới hạn số lượt
                'used_count'      => 0,
                'start_date'      => '2026-06-01',
                'end_date'        => '2026-08-31',
                'status'          => 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                // Hỗ trợ phí ship — giảm cố định 30K
                'coupon_code'     => 'FREESHIP',
                'type'            => 1,            // 1 = giảm tiền cố định
                'value'           => 30000.00,
                'max_discount'    => null,
                'min_order_value' => 300000,
                'max_usage'       => 2000,
                'used_count'      => 312,
                'start_date'      => '2024-01-01',
                'end_date'        => '2026-12-31',
                'status'          => 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                // Đơn lớn — giảm 200K cho đơn từ 2 triệu
                'coupon_code'     => 'FLASH200K',
                'type'            => 1,
                'value'           => 200000.00,
                'max_discount'    => null,
                'min_order_value' => 2000000,
                'max_usage'       => 500,
                'used_count'      => 47,
                'start_date'      => '2026-07-01',
                'end_date'        => '2026-09-30',
                'status'          => 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                // Tân khách — giảm 50K cho đơn đầu từ 500K
                'coupon_code'     => 'NEWUSER50K',
                'type'            => 1,
                'value'           => 50000.00,
                'max_discount'    => null,
                'min_order_value' => 500000,
                'max_usage'       => 300,
                'used_count'      => 89,
                'start_date'      => '2024-01-01',
                'end_date'        => '2026-12-31',
                'status'          => 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
        ]);
    }
}
