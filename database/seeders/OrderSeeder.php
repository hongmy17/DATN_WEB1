<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $userId1 = DB::table('users')->where('email', 'an.nguyen@gmail.com')->value('id');
        $userId2 = DB::table('users')->where('email', 'binh.tran@gmail.com')->value('id');
        $userId3 = DB::table('users')->where('email', 'cuong.le@gmail.com')->value('id');

        $addr1 = DB::table('user_addresses')->where('user_id', $userId1)->where('is_default', true)->first();
        $addr2 = DB::table('user_addresses')->where('user_id', $userId2)->where('is_default', true)->first();
        $addr3 = DB::table('user_addresses')->where('user_id', $userId3)->first();

        $coupon = DB::table('coupons')->where('coupon_code', 'WELCOME10')->first();

        // Lấy variant bằng đúng SKU đã tạo trong ProductSeeder (MacBook Pro 14" M3 Pro)
        $vBlk512 = DB::table('product_variants')->where('sku', 'MBP14-BLK-512')->first(); // 42,990,000
        $vBlk1TB = DB::table('product_variants')->where('sku', 'MBP14-BLK-1TB')->first(); // 52,990,000
        $vSlv512 = DB::table('product_variants')->where('sku', 'MBP14-SLV-512')->first(); // 42,990,000

        // Dừng sớm nếu thiếu dữ liệu bắt buộc
        if (! $vBlk512 || ! $vBlk1TB || ! $vSlv512) {
            $this->command->warn('OrderSeeder: Không tìm thấy product_variants. Hãy chạy ProductSeeder trước.');
            return;
        }

        // =====================================================================
        // Đơn hàng 1: Nguyễn Văn An – đã hoàn tất, có coupon WELCOME10 (-10%)
        // =====================================================================
        $subtotal1       = ($vBlk512->price * 1) + ($vBlk1TB->price * 1);
        $discountAmount1 = round($subtotal1 * 0.10);
        $total1          = $subtotal1 - $discountAmount1;

        $orderId1 = DB::table('orders')->insertGetId([
            'user_id'          => $userId1,
            'coupon_id'        => $coupon->id,
            'address_id'       => $addr1->id,
            'receiver_name'    => $addr1->receiver_name,
            'receiver_phone'   => $addr1->receiver_phone,
            'shipping_address' => "{$addr1->address_detail}, {$addr1->ward}, {$addr1->district}, {$addr1->province}",
            'order_status'     => 3, // hoàn tất
            'subtotal'         => $subtotal1,
            'discount_amount'  => $discountAmount1,
            'total_amount'     => $total1,
            'note'             => 'Giao giờ hành chính.',
            'created_at'       => $now->copy()->subDays(10),
            'updated_at'       => $now->copy()->subDays(8),
        ]);

        DB::table('order_items')->insert([
            [
                'order_id'            => $orderId1,
                'variant_id'          => $vBlk512->id,
                'product_name'        => 'MacBook Pro 14" M3 Pro',
                'variant_description' => 'Màu: Space Black / SSD: 512GB',
                'quantity'            => 1,
                'unit_price'          => $vBlk512->price,
                'total_price'         => $vBlk512->price,
                'created_at'          => $now->copy()->subDays(10),
            ],
            [
                'order_id'            => $orderId1,
                'variant_id'          => $vBlk1TB->id,
                'product_name'        => 'MacBook Pro 14" M3 Pro',
                'variant_description' => 'Màu: Space Black / SSD: 1TB',
                'quantity'            => 1,
                'unit_price'          => $vBlk1TB->price,
                'total_price'         => $vBlk1TB->price,
                'created_at'          => $now->copy()->subDays(10),
            ],
        ]);

        DB::table('payments')->insert([
            'order_id'         => $orderId1,
            'payment_gateway'  => 'VNPay',
            'transaction_code' => 'VNP202401100001',
            'amount'           => $total1,
            'status'           => 1,
            'gateway_response' => json_encode(['responseCode' => '00', 'message' => 'Giao dịch thành công']),
            'paid_at'          => $now->copy()->subDays(10)->addMinutes(5),
            'created_at'       => $now->copy()->subDays(10),
            'updated_at'       => $now->copy()->subDays(10)->addMinutes(5),
        ]);

        // =====================================================================
        // Đơn hàng 2: Trần Thị Bình – đang giao, COD, không coupon
        // =====================================================================
        $subtotal2 = $vSlv512->price * 1;

        $orderId2 = DB::table('orders')->insertGetId([
            'user_id'          => $userId2,
            'coupon_id'        => null,
            'address_id'       => $addr2->id,
            'receiver_name'    => $addr2->receiver_name,
            'receiver_phone'   => $addr2->receiver_phone,
            'shipping_address' => "{$addr2->address_detail}, {$addr2->ward}, {$addr2->district}, {$addr2->province}",
            'order_status'     => 2, // đang giao
            'subtotal'         => $subtotal2,
            'discount_amount'  => 0,
            'total_amount'     => $subtotal2,
            'note'             => null,
            'created_at'       => $now->copy()->subDays(2),
            'updated_at'       => $now->copy()->subDays(1),
        ]);

        DB::table('order_items')->insert([
            'order_id'            => $orderId2,
            'variant_id'          => $vSlv512->id,
            'product_name'        => 'MacBook Pro 14" M3 Pro',
            'variant_description' => 'Màu: Silver / SSD: 512GB',
            'quantity'            => 1,
            'unit_price'          => $vSlv512->price,
            'total_price'         => $vSlv512->price,
            'created_at'          => $now->copy()->subDays(2),
        ]);

        DB::table('payments')->insert([
            'order_id'         => $orderId2,
            'payment_gateway'  => 'COD',
            'transaction_code' => null,
            'amount'           => $subtotal2,
            'status'           => 0, // chờ (COD chưa nhận tiền)
            'gateway_response' => null,
            'paid_at'          => null,
            'created_at'       => $now->copy()->subDays(2),
            'updated_at'       => $now->copy()->subDays(2),
        ]);

        // =====================================================================
        // Đơn hàng 3: Lê Minh Cường – chờ xác nhận, Momo
        // =====================================================================
        $subtotal3 = $vBlk1TB->price * 2;

        $orderId3 = DB::table('orders')->insertGetId([
            'user_id'          => $userId3,
            'coupon_id'        => null,
            'address_id'       => $addr3->id,
            'receiver_name'    => $addr3->receiver_name,
            'receiver_phone'   => $addr3->receiver_phone,
            'shipping_address' => "{$addr3->address_detail}, {$addr3->ward}, {$addr3->district}, {$addr3->province}",
            'order_status'     => 0, // chờ xác nhận
            'subtotal'         => $subtotal3,
            'discount_amount'  => 0,
            'total_amount'     => $subtotal3,
            'note'             => 'Gói quà hộ.',
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);

        DB::table('order_items')->insert([
            'order_id'            => $orderId3,
            'variant_id'          => $vBlk1TB->id,
            'product_name'        => 'MacBook Pro 14" M3 Pro',
            'variant_description' => 'Màu: Space Black / SSD: 1TB',
            'quantity'            => 2,
            'unit_price'          => $vBlk1TB->price,
            'total_price'         => $vBlk1TB->price * 2,
            'created_at'          => $now,
        ]);

        DB::table('payments')->insert([
            'order_id'         => $orderId3,
            'payment_gateway'  => 'Momo',
            'transaction_code' => 'MOMO' . now()->format('YmdHis'),
            'amount'           => $subtotal3,
            'status'           => 1,
            'gateway_response' => json_encode(['resultCode' => 0, 'message' => 'Thành công']),
            'paid_at'          => $now,
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);
    }
}