<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Danh mục cha
        $parents = [
            ['name' => 'Thời Trang Nam',   'slug' => 'thoi-trang-nam'],
            ['name' => 'Thời Trang Nữ',    'slug' => 'thoi-trang-nu'],
            ['name' => 'Điện Tử',          'slug' => 'dien-tu'],
            ['name' => 'Gia Dụng',         'slug' => 'gia-dung'],
            ['name' => 'Mỹ Phẩm',          'slug' => 'my-pham'],
        ];

        foreach ($parents as $parent) {
            DB::table('categories')->insert([
                'name'        => $parent['name'],
                'slug'        => $parent['slug'],
                'parent_id'   => null,
                'description' => 'Danh mục ' . $parent['name'],
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        // Lấy ID các danh mục cha vừa tạo
        $namId  = DB::table('categories')->where('slug', 'thoi-trang-nam')->value('id');
        $nuId   = DB::table('categories')->where('slug', 'thoi-trang-nu')->value('id');
        $dienId = DB::table('categories')->where('slug', 'dien-tu')->value('id');

        // Danh mục con
        $children = [
            ['name' => 'Áo Nam',        'slug' => 'ao-nam',         'parent_id' => $namId],
            ['name' => 'Quần Nam',       'slug' => 'quan-nam',       'parent_id' => $namId],
            ['name' => 'Giày Nam',       'slug' => 'giay-nam',       'parent_id' => $namId],
            ['name' => 'Áo Nữ',         'slug' => 'ao-nu',          'parent_id' => $nuId],
            ['name' => 'Đầm Váy',        'slug' => 'dam-vay',        'parent_id' => $nuId],
            ['name' => 'Giày Nữ',        'slug' => 'giay-nu',        'parent_id' => $nuId],
            ['name' => 'Điện Thoại',     'slug' => 'dien-thoai',     'parent_id' => $dienId],
            ['name' => 'Laptop',         'slug' => 'laptop',         'parent_id' => $dienId],
            ['name' => 'Phụ Kiện Điện Tử', 'slug' => 'phu-kien-dien-tu', 'parent_id' => $dienId],
        ];

        foreach ($children as $child) {
            DB::table('categories')->insert([
                'name'        => $child['name'],
                'slug'        => $child['slug'],
                'parent_id'   => $child['parent_id'],
                'description' => 'Danh mục ' . $child['name'],
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }
    }
}
