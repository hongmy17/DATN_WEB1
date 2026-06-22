<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // ── Lấy user ──────────────────────────────────────────────────────
        $userId1 = DB::table('users')->where('email', 'an.nguyen@gmail.com')->value('id');
        $userId2 = DB::table('users')->where('email', 'binh.tran@gmail.com')->value('id');
        $userId3 = DB::table('users')->where('email', 'cuong.le@gmail.com')->value('id');

        // ── Lấy địa chỉ mặc định của từng user ───────────────────────────
        $addr1 = DB::table('user_addresses')->where('user_id', $userId1)->where('is_default', true)->first();
        $addr2 = DB::table('user_addresses')->where('user_id', $userId2)->where('is_default', true)->first();
        $addr3 = DB::table('user_addresses')->where('user_id', $userId3)->first();

        // ── Lấy coupon ────────────────────────────────────────────────────
        $couponWelcome = DB::table('coupons')->where('coupon_code', 'WELCOME10')->first();
        $couponFreeship = DB::table('coupons')->where('coupon_code', 'FREESHIP')->first();

        // ── Lấy variant theo SKU ──────────────────────────────────────────
        $vMXM  = DB::table('product_variants')->where('sku', 'MXM3S-BLK-WL')->first();   // MX Master 3S Đen WL
        $vDAV  = DB::table('product_variants')->where('sku', 'DAV3-WHT-USB')->first();   // DeathAdder V3 Trắng
        $vWH1  = DB::table('product_variants')->where('sku', 'WH1K-BLK-USBC')->first();  // Sony WH-1000XM5 Đen
        $vG733 = DB::table('product_variants')->where('sku', 'G733-PNK-WL')->first();    // G733 Hồng
        $vK2P  = DB::table('product_variants')->where('sku', 'K2P-WHT-TKL')->first();    // Keychron K2 Pro Trắng TKL
        $vAK   = DB::table('product_variants')->where('sku', 'AK3087-BLK-FULL')->first(); // Akko 3087 Đen Full

        if (!$vMXM || !$vDAV || !$vWH1 || !$vG733 || !$vK2P || !$vAK) {
            $this->command->warn('OrderSeeder: Thiếu variant. Hãy chạy ProductSeeder trước.');
            return;
        }

        // ══════════════════════════════════════════════════════════════════
        //  Đơn hàng 1: Nguyễn Văn An — ĐÃ HOÀN TẤT
        //  Mua: MX Master 3S (1) + Sony WH-1000XM5 (1)
        //  Thanh toán: VNPay | Coupon: WELCOME10 (-10%)
        // ══════════════════════════════════════════════════════════════════
        $sub1      = ($vMXM->price * 1) + ($vWH1->price * 1); // 1.590.000 + 8.490.000
        $discount1 = round($sub1 * 0.10);                      // -10% = 1.008.000
        $total1    = $sub1 - $discount1;

        $orderId1 = DB::table('orders')->insertGetId([
            'user_id'         => $userId1,
            'coupon_id'       => $couponWelcome->id,
            'address_id'      => $addr1->id,
            // SNAPSHOT địa chỉ — copy tại thời điểm đặt
            // SNAPSHOT địa chỉ
            'receiver_name'  => $addr1->receiver_name,
            'receiver_phone' => $addr1->receiver_phone,

            'shipping_address' =>
            $addr1->address_detail . ', ' .
                $addr1->ward . ', ' .
                $addr1->district . ', ' .
                $addr1->province,

            'order_status' => 3,

            'subtotal' => $sub1,
            'discount_amount' => $discount1,
            'total_amount'    => $total1,
            'note'            => 'Giao giờ hành chính, gọi trước khi giao.',
            'created_at'      => $now->copy()->subDays(10),
            'updated_at'      => $now->copy()->subDays(8),
        ]);

        DB::table('order_items')->insert([
            [
                'order_id'            => $orderId1,
                'variant_id'          => $vMXM->id,
                // SNAPSHOT sản phẩm — copy tên và giá tại thời điểm đặt
                'product_name'        => 'Chuột Logitech MX Master 3S',
                'variant_description' => 'Màu sắc: Đen, Kết nối: Không dây (2.4GHz)',
                'quantity'            => 1,
                'unit_price'          => $vMXM->price,
                'total_price'         => $vMXM->price * 1,
                'created_at'          => $now->copy()->subDays(10),
            ],
            [
                'order_id'            => $orderId1,
                'variant_id'          => $vWH1->id,
                'product_name'        => 'Tai Nghe Sony WH-1000XM5',
                'variant_description' => 'Màu sắc: Đen, Cổng kết nối: USB-C',
                'quantity'            => 1,
                'unit_price'          => $vWH1->price,
                'total_price'         => $vWH1->price * 1,
                'created_at'          => $now->copy()->subDays(10),
            ],
        ]);

        DB::table('payments')->insert([
            'order_id'         => $orderId1,
            'payment_gateway'  => 'VNPay',
            'transaction_code' => 'VNP' . now()->format('YmdHis') . '001',
            'amount'           => $total1,
            'status'           => 1, // đã thanh toán
            'gateway_response' => json_encode(['responseCode' => '00', 'message' => 'Giao dịch thành công']),
            'paid_at'          => $now->copy()->subDays(10)->addMinutes(3),
            'created_at'       => $now->copy()->subDays(10),
            'updated_at'       => $now->copy()->subDays(10)->addMinutes(3),
        ]);

        // ══════════════════════════════════════════════════════════════════
        //  Đơn hàng 2: Trần Thị Bình — ĐANG GIAO
        //  Mua: Keychron K2 Pro (1) + Akko 3087 (1)
        //  Thanh toán: COD | Coupon: FREESHIP (-30.000đ)
        // ══════════════════════════════════════════════════════════════════
        $sub2      = ($vK2P->price * 1) + ($vAK->price * 1); // 1.890.000 + 990.000
        $discount2 = 30000;                                    // freeship cố định
        $total2    = $sub2 - $discount2;

        $orderId2 = DB::table('orders')->insertGetId([
            'user_id'         => $userId2,
            'coupon_id'       => $couponFreeship->id,
            'address_id'      => $addr2->id,
            // SNAPSHOT địa chỉ
            'receiver_name'  => $addr2->receiver_name,
            'receiver_phone' => $addr2->receiver_phone,

            'shipping_address' =>
            $addr2->address_detail . ', ' .
                $addr2->ward . ', ' .
                $addr2->district . ', ' .
                $addr2->province,

            'order_status' => 3,

            'subtotal' => $sub2,
            'discount_amount' => $discount2,
            'total_amount'    => $total2,
            'note'            => null,
            'created_at'      => $now->copy()->subDays(2),
            'updated_at'      => $now->copy()->subDays(1),
        ]);

        DB::table('order_items')->insert([
            [
                'order_id'            => $orderId2,
                'variant_id'          => $vK2P->id,
                'product_name'        => 'Bàn Phím Cơ Keychron K2 Pro',
                'variant_description' => 'Màu sắc: Trắng, Layout: TKL (80%)',
                'quantity'            => 1,
                'unit_price'          => $vK2P->price,
                'total_price'         => $vK2P->price,
                'created_at'          => $now->copy()->subDays(2),
            ],
            [
                'order_id'            => $orderId2,
                'variant_id'          => $vAK->id,
                'product_name'        => 'Bàn Phím Cơ Akko 3087',
                'variant_description' => 'Màu sắc: Đen, Layout: Full-size (100%)',
                'quantity'            => 1,
                'unit_price'          => $vAK->price,
                'total_price'         => $vAK->price,
                'created_at'          => $now->copy()->subDays(2),
            ],
        ]);

        DB::table('payments')->insert([
            'order_id'         => $orderId2,
            'payment_gateway'  => 'COD',
            'transaction_code' => null,
            'amount'           => $total2,
            'status'           => 0, // chờ thu tiền khi giao
            'gateway_response' => null,
            'paid_at'          => null,
            'created_at'       => $now->copy()->subDays(2),
            'updated_at'       => $now->copy()->subDays(2),
        ]);

        // ══════════════════════════════════════════════════════════════════
        //  Đơn hàng 3: Lê Minh Cường — CHỜ XÁC NHẬN
        //  Mua: Razer DeathAdder V3 (1) + G733 Hồng (1)
        //  Thanh toán: MoMo | Không coupon
        // ══════════════════════════════════════════════════════════════════
        $sub3   = ($vDAV->price * 1) + ($vG733->price * 1); // 1.690.000 + 2.490.000
        $total3 = $sub3;

        $orderId3 = DB::table('orders')->insertGetId([
            'user_id'         => $userId3,
            'coupon_id'       => null,
            // SNAPSHOT địa chỉ
            'receiver_name'  => $addr3->receiver_name,
            'receiver_phone' => $addr3->receiver_phone,

            'shipping_address' =>
            $addr3->address_detail . ', ' .
                $addr3->ward . ', ' .
                $addr3->district . ', ' .
                $addr3->province,

            'order_status' => 3,

            'subtotal' => $sub3,
            'discount_amount' => 0,
            'total_amount'    => $total3,
            'note'            => 'Gói quà hộ, không ghi giá trên hộp.',
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);

        DB::table('order_items')->insert([
            [
                'order_id'            => $orderId3,
                'variant_id'          => $vDAV->id,
                'product_name'        => 'Chuột Gaming Razer DeathAdder V3',
                'variant_description' => 'Màu sắc: Trắng, Kết nối: Có dây (USB-A)',
                'quantity'            => 1,
                'unit_price'          => $vDAV->price,
                'total_price'         => $vDAV->price,
                'created_at'          => $now,
            ],
            [
                'order_id'            => $orderId3,
                'variant_id'          => $vG733->id,
                'product_name'        => 'Tai Nghe Gaming Logitech G733',
                'variant_description' => 'Màu sắc: Hồng, Cổng kết nối: Wireless 2.4GHz',
                'quantity'            => 1,
                'unit_price'          => $vG733->price,
                'total_price'         => $vG733->price,
                'created_at'          => $now,
            ],
        ]);

        DB::table('payments')->insert([
            'order_id'         => $orderId3,
            'payment_gateway'  => 'Momo',
            'transaction_code' => 'MOMO' . now()->format('YmdHis'),
            'amount'           => $total3,
            'status'           => 1,
            'gateway_response' => json_encode(['resultCode' => 0, 'message' => 'Thành công']),
            'paid_at'          => $now,
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);
    }
}
