<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            UserSeeder::class,
            AttributeSeeder::class,
            ProductSeeder::class,       // bao gồm product_images, product_variants, variant_attribute_values
            UserAddressSeeder::class,
            CartItemSeeder::class,
            CouponSeeder::class,
            OrderSeeder::class,         // bao gồm order_items, payments
        ]);
    }
}
