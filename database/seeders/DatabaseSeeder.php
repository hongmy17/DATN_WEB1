<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Tắt kiểm tra foreign key trước khi seed
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            AttributeSeeder::class,
            ProductSeeder::class,
            CouponSeeder::class,
            UserAddressSeeder::class,
            //OrderSeeder::class,
            CartItemSeeder::class,
        ]);

        // Bật lại sau khi seed xong
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
