<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->truncate();
        $now = now();

        // ── 4 DANH MỤC CHA ───────────────────────────────────────────────
        $parents = [
            ['name' => 'Âm thanh',              'slug' => 'am-thanh',              'description' => 'Tai nghe, loa di động và thiết bị âm thanh cao cấp'],
            ['name' => 'Phụ kiện PC & Laptop',  'slug' => 'phu-kien-pc-laptop',    'description' => 'Chuột, bàn phím, webcam và phụ kiện máy tính'],
            ['name' => 'Sạc & Cáp',             'slug' => 'sac-va-cap',            'description' => 'Sạc nhanh, pin dự phòng, cáp và hub kết nối'],
            ['name' => 'Thiết bị đeo',          'slug' => 'thiet-bi-deo',          'description' => 'Smartwatch và vòng tay thể thao thông minh'],
        ];

        foreach ($parents as $p) {
            DB::table('categories')->insert([
                'name' => $p['name'],
                'slug' => $p['slug'],
                'parent_id' => null,
                'description' => $p['description'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $amThanh    = DB::table('categories')->where('slug', 'am-thanh')->value('id');
        $ngoaiVi    = DB::table('categories')->where('slug', 'phu-kien-pc-laptop')->value('id');
        $sacCap     = DB::table('categories')->where('slug', 'sac-va-cap')->value('id');
        $thietBiDeo = DB::table('categories')->where('slug', 'thiet-bi-deo')->value('id');

        // ── 11 DANH MỤC CON ──────────────────────────────────────────────
        $children = [
            // Âm thanh
            ['name' => 'Tai nghe chống ồn',      'slug' => 'tai-nghe-chong-on',     'parent_id' => $amThanh,    'description' => 'Tai nghe over-ear chống ồn chủ động — Sony, Bose, Jabra'],
            ['name' => 'Tai nghe true wireless',  'slug' => 'tai-nghe-true-wireless', 'parent_id' => $amThanh,    'description' => 'Tai nghe không dây hoàn toàn — AirPods, Sony, Samsung Buds'],
            ['name' => 'Loa di động',             'slug' => 'loa-di-dong',            'parent_id' => $amThanh,    'description' => 'Loa Bluetooth di động chống nước — JBL, Marshall, Bose'],
            // Ngoại vi
            ['name' => 'Chuột',                   'slug' => 'chuot',                  'parent_id' => $ngoaiVi,    'description' => 'Chuột văn phòng và gaming — Logitech, Razer, Apple'],
            ['name' => 'Bàn phím',                'slug' => 'ban-phim',               'parent_id' => $ngoaiVi,    'description' => 'Bàn phím cơ và membrane — Logitech, Apple, Keychron'],
            ['name' => 'Webcam & Micro',          'slug' => 'webcam-micro',           'parent_id' => $ngoaiVi,    'description' => 'Webcam và micro dành cho streaming, họp online'],
            // Sạc & Cáp
            ['name' => 'Sạc nhanh',               'slug' => 'sac-nhanh',              'parent_id' => $sacCap,     'description' => 'Củ sạc GaN tốc độ cao — Anker, Apple, Samsung'],
            ['name' => 'Pin dự phòng',            'slug' => 'pin-du-phong',           'parent_id' => $sacCap,     'description' => 'Pin dự phòng dung lượng lớn — Anker, Xiaomi, Baseus'],
            ['name' => 'Cáp & Hub USB-C',         'slug' => 'cap-hub-usb-c',          'parent_id' => $sacCap,     'description' => 'Cáp sạc, cáp dữ liệu và hub đa năng — Anker, Ugreen'],
            // Thiết bị đeo
            ['name' => 'Smartwatch',              'slug' => 'smartwatch',             'parent_id' => $thietBiDeo, 'description' => 'Đồng hồ thông minh — Apple Watch, Samsung Galaxy Watch'],
            ['name' => 'Vòng tay thể thao',      'slug' => 'vong-tay-the-thao',     'parent_id' => $thietBiDeo, 'description' => 'Vòng tay theo dõi sức khỏe — Xiaomi Band, Fitbit'],
        ];

        foreach ($children as $c) {
            DB::table('categories')->insert([
                'name' => $c['name'],
                'slug' => $c['slug'],
                'parent_id' => $c['parent_id'],
                'description' => $c['description'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
