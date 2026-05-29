<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $now     = now();
        $adminId = DB::table('users')->where('role', 1)->value('id');

        // Lấy ID danh mục
        $catAoNam  = DB::table('categories')->where('slug', 'ao-nam')->value('id');
        $catAoNu   = DB::table('categories')->where('slug', 'ao-nu')->value('id');
        $catDienThoai = DB::table('categories')->where('slug', 'dien-thoai')->value('id');
        $catLaptop = DB::table('categories')->where('slug', 'laptop')->value('id');
        $catDamVay = DB::table('categories')->where('slug', 'dam-vay')->value('id');

        // Lấy ID attribute_values
        $avDo    = DB::table('attribute_values')->where('value', 'Đỏ')->value('id');
        $avXanh  = DB::table('attribute_values')->where('value', 'Xanh Dương')->value('id');
        $avDen   = DB::table('attribute_values')->where('value', 'Đen')->value('id');
        $avTrang = DB::table('attribute_values')->where('value', 'Trắng')->value('id');
        $avM     = DB::table('attribute_values')->where('value', 'M')->value('id');
        $avL     = DB::table('attribute_values')->where('value', 'L')->value('id');
        $avXL    = DB::table('attribute_values')->where('value', 'XL')->value('id');

        // =====================================================================
        // Sản phẩm 1: Áo Polo Nam Classic
        // =====================================================================
        $p1 = DB::table('products')->insertGetId([
            'code'              => 'PRD0000001',
            'category_id'       => $catAoNam,
            'name'              => 'Áo Polo Nam Classic',
            'slug'              => 'ao-polo-nam-classic',
            'short_description' => 'Áo polo nam chất liệu cotton cao cấp, thoáng mát.',
            'description'       => '<p>Áo polo nam thiết kế classic, chất liệu 100% cotton co giãn 4 chiều, phù hợp đi làm và dạo phố.</p>',
            'thumbnail'         => 'products/ao-polo-nam-classic.jpg',
            'status'            => 1,
            'created_by'        => $adminId,
            'created_at'        => $now,
            'updated_at'        => $now,
            'delete_at'         => null,
        ]);

        // Ảnh sản phẩm 1
        foreach ([
            ['ao-polo-1.jpg', true,  1],
            ['ao-polo-2.jpg', false, 2],
            ['ao-polo-3.jpg', false, 3],
        ] as [$img, $isPrimary, $order]) {
            DB::table('product_images')->insert([
                'product_id' => $p1,
                'image_url'  => "products/$img",
                'is_primary' => $isPrimary,
                'sort_order' => $order,
                'created_at' => $now,
            ]);
        }

        // Biến thể sản phẩm 1: Màu Đỏ × M, L, XL
        $variants1 = [
            ['sku' => 'POL-RED-M',  'price' => 299000, 'compare_price' => 399000, 'stock' => 50, 'avColor' => $avDo,  'avSize' => $avM],
            ['sku' => 'POL-RED-L',  'price' => 299000, 'compare_price' => 399000, 'stock' => 40, 'avColor' => $avDo,  'avSize' => $avL],
            ['sku' => 'POL-RED-XL', 'price' => 299000, 'compare_price' => 399000, 'stock' => 30, 'avColor' => $avDo,  'avSize' => $avXL],
            ['sku' => 'POL-BLU-M',  'price' => 299000, 'compare_price' => 399000, 'stock' => 45, 'avColor' => $avXanh,'avSize' => $avM],
            ['sku' => 'POL-BLU-L',  'price' => 299000, 'compare_price' => 399000, 'stock' => 35, 'avColor' => $avXanh,'avSize' => $avL],
        ];

        foreach ($variants1 as $v) {
            $vId = DB::table('product_variants')->insertGetId([
                'product_id'     => $p1,
                'sku'            => $v['sku'],
                'price'          => $v['price'],
                'compare_price'  => $v['compare_price'],
                'stock_quantity' => $v['stock'],
                'image'          => null,
                'status'         => 1,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);

            DB::table('variant_attribute_values')->insert([
                ['variant_id' => $vId, 'attribute_value_id' => $v['avColor'], 'created_at' => $now],
                ['variant_id' => $vId, 'attribute_value_id' => $v['avSize'],  'created_at' => $now],
            ]);
        }

        // =====================================================================
        // Sản phẩm 2: Áo Thun Nữ Oversize
        // =====================================================================
        $p2 = DB::table('products')->insertGetId([
            'code'              => 'PRD0000002',
            'category_id'       => $catAoNu,
            'name'              => 'Áo Thun Nữ Oversize',
            'slug'              => 'ao-thun-nu-oversize',
            'short_description' => 'Áo thun nữ form rộng, phong cách ulzzang Hàn Quốc.',
            'description'       => '<p>Áo thun nữ oversize chất liệu cotton mềm mịn, thiết kế trẻ trung.</p>',
            'thumbnail'         => 'products/ao-thun-nu-oversize.jpg',
            'status'            => 1,
            'created_by'        => $adminId,
            'created_at'        => $now,
            'updated_at'        => $now,
            'delete_at'         => null,
        ]);

        foreach ([
            ['thun-nu-1.jpg', true,  1],
            ['thun-nu-2.jpg', false, 2],
        ] as [$img, $isPrimary, $order]) {
            DB::table('product_images')->insert([
                'product_id' => $p2,
                'image_url'  => "products/$img",
                'is_primary' => $isPrimary,
                'sort_order' => $order,
                'created_at' => $now,
            ]);
        }

        $variants2 = [
            ['sku' => 'THN-BLK-M', 'price' => 185000, 'compare_price' => 250000, 'stock' => 60, 'avColor' => $avDen,  'avSize' => $avM],
            ['sku' => 'THN-BLK-L', 'price' => 185000, 'compare_price' => 250000, 'stock' => 50, 'avColor' => $avDen,  'avSize' => $avL],
            ['sku' => 'THN-WHT-M', 'price' => 185000, 'compare_price' => 250000, 'stock' => 55, 'avColor' => $avTrang,'avSize' => $avM],
            ['sku' => 'THN-WHT-L', 'price' => 185000, 'compare_price' => 250000, 'stock' => 45, 'avColor' => $avTrang,'avSize' => $avL],
        ];

        foreach ($variants2 as $v) {
            $vId = DB::table('product_variants')->insertGetId([
                'product_id'     => $p2,
                'sku'            => $v['sku'],
                'price'          => $v['price'],
                'compare_price'  => $v['compare_price'],
                'stock_quantity' => $v['stock'],
                'image'          => null,
                'status'         => 1,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);

            DB::table('variant_attribute_values')->insert([
                ['variant_id' => $vId, 'attribute_value_id' => $v['avColor'], 'created_at' => $now],
                ['variant_id' => $vId, 'attribute_value_id' => $v['avSize'],  'created_at' => $now],
            ]);
        }

        // =====================================================================
        // Sản phẩm 3: Điện Thoại Samsung Galaxy A55
        // =====================================================================
        $p3 = DB::table('products')->insertGetId([
            'code'              => 'PRD0000003',
            'category_id'       => $catDienThoai,
            'name'              => 'Samsung Galaxy A55',
            'slug'              => 'samsung-galaxy-a55',
            'short_description' => 'Smartphone tầm trung cao cấp, màn hình AMOLED 6.6 inch.',
            'description'       => '<p>Samsung Galaxy A55 với chip Exynos 1480, RAM 8GB, bộ nhớ 128GB, camera 50MP.</p>',
            'thumbnail'         => 'products/samsung-a55.jpg',
            'status'            => 1,
            'created_by'        => $adminId,
            'created_at'        => $now,
            'updated_at'        => $now,
            'delete_at'         => null,
        ]);

        DB::table('product_images')->insert([
            'product_id' => $p3,
            'image_url'  => 'products/samsung-a55-1.jpg',
            'is_primary' => true,
            'sort_order' => 1,
            'created_at' => $now,
        ]);

        // Biến thể điện thoại chỉ theo màu (không có size)
        foreach ([
            ['sku' => 'SAM-A55-BLK', 'price' => 9490000, 'compare_price' => 10990000, 'stock' => 20, 'avColor' => $avDen],
            ['sku' => 'SAM-A55-BLU', 'price' => 9490000, 'compare_price' => 10990000, 'stock' => 15, 'avColor' => $avXanh],
        ] as $v) {
            $vId = DB::table('product_variants')->insertGetId([
                'product_id'     => $p3,
                'sku'            => $v['sku'],
                'price'          => $v['price'],
                'compare_price'  => $v['compare_price'],
                'stock_quantity' => $v['stock'],
                'image'          => null,
                'status'         => 1,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);

            DB::table('variant_attribute_values')->insert([
                'variant_id' => $vId, 'attribute_value_id' => $v['avColor'], 'created_at' => $now,
            ]);
        }
    }
}
