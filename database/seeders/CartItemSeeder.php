<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartItemSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Lấy user
        $userId1 = DB::table('users')->where('email', 'an.nguyen@gmail.com')->value('id');
        $userId2 = DB::table('users')->where('email', 'binh.tran@gmail.com')->value('id');
        $userId3 = DB::table('users')->where('email', 'cuong.le@gmail.com')->value('id');

        // Lấy variant theo SKU (các SKU này khớp với ProductSeeder mới)
        // User 1 (An): bỏ chuột + tai nghe vào giỏ
        $v1 = DB::table('product_variants')->where('sku', 'MXM3S-BLK-WL')->value('id');   // MX Master 3S Đen Không dây
        $v2 = DB::table('product_variants')->where('sku', 'WH1K-BLK-USBC')->value('id');  // Sony WH1000XM5 Đen USB-C

        // User 2 (Bình): bỏ bàn phím vào giỏ
        $v3 = DB::table('product_variants')->where('sku', 'K2P-WHT-TKL')->value('id');    // Keychron K2 Pro Trắng TKL
        $v4 = DB::table('product_variants')->where('sku', 'AK3087-PNK-TKL')->value('id'); // Akko 3087 Hồng TKL

        // User 3 (Cường): bỏ tai nghe gaming
        $v5 = DB::table('product_variants')->where('sku', 'G733-BLK-WL')->value('id');    // G733 Đen

        $items = array_filter([
            $v1 ? ['user_id' => $userId1, 'variant_id' => $v1, 'quantity' => 1] : null,
            $v2 ? ['user_id' => $userId1, 'variant_id' => $v2, 'quantity' => 1] : null,
            $v3 ? ['user_id' => $userId2, 'variant_id' => $v3, 'quantity' => 1] : null,
            $v4 ? ['user_id' => $userId2, 'variant_id' => $v4, 'quantity' => 2] : null,
            $v5 ? ['user_id' => $userId3, 'variant_id' => $v5, 'quantity' => 1] : null,
        ]);

        foreach ($items as $item) {
            DB::table('cart_items')->insert(array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }
}
