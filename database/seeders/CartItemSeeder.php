<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartItemSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $userId1 = DB::table('users')->where('email', 'an.nguyen@gmail.com')->value('id');
        $userId2 = DB::table('users')->where('email', 'binh.tran@gmail.com')->value('id');

        // Dùng đúng SKU được tạo trong ProductSeeder (MacBook Pro 14" M3 Pro)
        $vBlk512 = DB::table('product_variants')->where('sku', 'MBP14-BLK-512')->value('id');
        $vBlk1TB = DB::table('product_variants')->where('sku', 'MBP14-BLK-1TB')->value('id');
        $vSlv512 = DB::table('product_variants')->where('sku', 'MBP14-SLV-512')->value('id');

        // Bỏ qua nếu variant không tồn tại
        $cartItems = array_filter([
            $vBlk512 ? ['user_id' => $userId1, 'variant_id' => $vBlk512, 'quantity' => 1] : null,
            $vBlk1TB ? ['user_id' => $userId1, 'variant_id' => $vBlk1TB, 'quantity' => 2] : null,
            $vSlv512 ? ['user_id' => $userId2, 'variant_id' => $vSlv512, 'quantity' => 1] : null,
        ]);

        foreach ($cartItems as $item) {
            DB::table('cart_items')->insert(array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }
}