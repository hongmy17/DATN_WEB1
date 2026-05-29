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

        $vPolRedM  = DB::table('product_variants')->where('sku', 'POL-RED-M')->value('id');
        $vThnWhtL  = DB::table('product_variants')->where('sku', 'THN-WHT-L')->value('id');
        $vSamBlu   = DB::table('product_variants')->where('sku', 'SAM-A55-BLU')->value('id');

        $cartItems = [
            ['user_id' => $userId1, 'variant_id' => $vPolRedM, 'quantity' => 1],
            ['user_id' => $userId1, 'variant_id' => $vThnWhtL, 'quantity' => 2],
            ['user_id' => $userId2, 'variant_id' => $vSamBlu,  'quantity' => 1],
        ];

        foreach ($cartItems as $item) {
            DB::table('cart_items')->insert(array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }
}
