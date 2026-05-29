<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $users    = DB::table('users')->where('role', 0)->get()->keyBy('id');
        $userId1  = DB::table('users')->where('email', 'an.nguyen@gmail.com')->value('id');
        $userId2  = DB::table('users')->where('email', 'binh.tran@gmail.com')->value('id');
        $userId3  = DB::table('users')->where('email', 'cuong.le@gmail.com')->value('id');

        $addr1    = DB::table('user_addresses')->where('user_id', $userId1)->where('is_default', true)->first();
        $addr2    = DB::table('user_addresses')->where('user_id', $userId2)->where('is_default', true)->first();

        $coupon   = DB::table('coupons')->where('coupon_code', 'WELCOME10')->first();

        // Lấy variant IDs
        $vPolRedM   = DB::table('product_variants')->where('sku', 'POL-RED-M')->first();
        $vPolBluL   = DB::table('product_variants')->where('sku', 'POL-BLU-L')->first();
        $vThnBlkM   = DB::table('product_variants')->where('sku', 'THN-BLK-M')->first();
        $vSamBlk    = DB::table('product_variants')->where('sku', 'SAM-A55-BLK')->first();

        // =====================================================================
        // Đơn hàng 1: Nguyễn Văn An – đã hoàn tất, có coupon
        // =====================================================================
        $subtotal1       = ($vPolRedM->price * 2) + ($vThnBlkM->price * 1);
        $discountAmount1 = round($subtotal1 * 0.10); // WELCOME10 giảm 10%
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
                'variant_id'          => $vPolRedM->id,
                'product_name'        => 'Áo Polo Nam Classic',
                'variant_description' => 'Màu: Đỏ / Size: M',
                'quantity'            => 2,
                'unit_price'          => $vPolRedM->price,
                'total_price'         => $vPolRedM->price * 2,
                'created_at'          => $now->copy()->subDays(10),
            ],
            [
                'order_id'            => $orderId1,
                'variant_id'          => $vThnBlkM->id,
                'product_name'        => 'Áo Thun Nữ Oversize',
                'variant_description' => 'Màu: Đen / Size: M',
                'quantity'            => 1,
                'unit_price'          => $vThnBlkM->price,
                'total_price'         => $vThnBlkM->price * 1,
                'created_at'          => $now->copy()->subDays(10),
            ],
        ]);

        DB::table('payments')->insert([
            'order_id'         => $orderId1,
            'payment_gateway'  => 'VNPay',
            'transaction_code' => 'VNP202401100001',
            'amount'           => $total1,
            'status'           => 1, // thành công
            'gateway_response' => json_encode(['responseCode' => '00', 'message' => 'Giao dịch thành công']),
            'paid_at'          => $now->copy()->subDays(10)->addMinutes(5),
            'created_at'       => $now->copy()->subDays(10),
            'updated_at'       => $now->copy()->subDays(10)->addMinutes(5),
        ]);

        // =====================================================================
        // Đơn hàng 2: Trần Thị Bình – đang giao, COD, không coupon
        // =====================================================================
        $subtotal2 = $vSamBlk->price * 1;

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
            'variant_id'          => $vSamBlk->id,
            'product_name'        => 'Samsung Galaxy A55',
            'variant_description' => 'Màu: Đen',
            'quantity'            => 1,
            'unit_price'          => $vSamBlk->price,
            'total_price'         => $vSamBlk->price,
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
        $subtotal3 = $vPolBluL->price * 3;

        $addr3 = DB::table('user_addresses')->where('user_id', $userId3)->first();

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
            'variant_id'          => $vPolBluL->id,
            'product_name'        => 'Áo Polo Nam Classic',
            'variant_description' => 'Màu: Xanh Dương / Size: L',
            'quantity'            => 3,
            'unit_price'          => $vPolBluL->price,
            'total_price'         => $vPolBluL->price * 3,
            'created_at'          => $now,
        ]);

        DB::table('payments')->insert([
            'order_id'         => $orderId3,
            'payment_gateway'  => 'Momo',
            'transaction_code' => 'MOMO' . now()->format('YmdHis'),
            'amount'           => $subtotal3,
            'status'           => 1, // đã thanh toán
            'gateway_response' => json_encode(['resultCode' => 0, 'message' => 'Thành công']),
            'paid_at'          => $now,
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);
    }
}
