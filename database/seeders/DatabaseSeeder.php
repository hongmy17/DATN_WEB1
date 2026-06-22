<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            CategorySeeder::class,       // 1. Danh mục (phải trước Product)
            UserSeeder::class,           // 2. Users
            AttributeSeeder::class,      // 3. Attributes (phải trước Product)
            ProductSeeder::class,        // 4. Products + Variants + Images
            UserAddressSeeder::class,    // 5. Địa chỉ users
            CartItemSeeder::class,       // 6. Giỏ hàng
            CouponSeeder::class,         // 7. Mã giảm giá (phải trước Order)
            OrderSeeder::class,          // 8. Đơn hàng + Items + Payments
        ]);
    }
}
