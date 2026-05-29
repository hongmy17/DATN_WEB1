<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Thuộc tính
        $attributes = [
            ['name' => 'Màu Sắc',  'display_type' => 1], // hiển thị dạng màu
            ['name' => 'Kích Thước', 'display_type' => 0], // hiển thị dạng text
            ['name' => 'Chất Liệu',  'display_type' => 0],
        ];

        foreach ($attributes as $attr) {
            DB::table('attributes')->insert([
                'name'         => $attr['name'],
                'display_type' => $attr['display_type'],
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }

        $mauId      = DB::table('attributes')->where('name', 'Màu Sắc')->value('id');
        $kichId     = DB::table('attributes')->where('name', 'Kích Thước')->value('id');
        $chatId     = DB::table('attributes')->where('name', 'Chất Liệu')->value('id');

        // Giá trị thuộc tính: Màu Sắc
        $colors = [
            ['value' => 'Đỏ',    'color_code' => '#FF0000', 'sort_order' => 1],
            ['value' => 'Xanh Dương', 'color_code' => '#0000FF', 'sort_order' => 2],
            ['value' => 'Xanh Lá', 'color_code' => '#008000', 'sort_order' => 3],
            ['value' => 'Đen',   'color_code' => '#000000', 'sort_order' => 4],
            ['value' => 'Trắng', 'color_code' => '#FFFFFF', 'sort_order' => 5],
            ['value' => 'Vàng',  'color_code' => '#FFD700', 'sort_order' => 6],
        ];

        foreach ($colors as $c) {
            DB::table('attribute_values')->insert([
                'attribute_id' => $mauId,
                'value'        => $c['value'],
                'color_code'   => $c['color_code'],
                'sort_order'   => $c['sort_order'],
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }

        // Giá trị thuộc tính: Kích Thước
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
        foreach ($sizes as $i => $size) {
            DB::table('attribute_values')->insert([
                'attribute_id' => $kichId,
                'value'        => $size,
                'color_code'   => null,
                'sort_order'   => $i + 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }

        // Giá trị thuộc tính: Chất Liệu
        $materials = ['Cotton', 'Polyester', 'Linen', 'Denim', 'Silk'];
        foreach ($materials as $i => $mat) {
            DB::table('attribute_values')->insert([
                'attribute_id' => $chatId,
                'value'        => $mat,
                'color_code'   => null,
                'sort_order'   => $i + 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }
    }
}
