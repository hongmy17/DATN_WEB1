<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Danh mục cha
        $parents = [
            [
                'name' => 'Thiết Bị Ngoại Vi',
                'slug' => 'thiet-bi-ngoai-vi',
                'description' => 'Chuột, bàn phím, tai nghe và các thiết bị ngoại vi máy tính',
            ],
            [
                'name' => 'Thiết Bị Crypto',
                'slug' => 'thiet-bi-crypto',
                'description' => 'Ví lạnh, khóa bảo mật và thiết bị lưu trữ tài sản số',
            ],
            [
                'name' => 'Phụ Kiện Công Nghệ',
                'slug' => 'phu-kien-cong-nghe',
                'description' => 'Cáp, túi đựng, phụ kiện bảo vệ thiết bị',
            ],
        ];

        foreach ($parents as $parent) {
            DB::table('categories')->insert([
                'name' => $parent['name'],
                'slug' => $parent['slug'],
                'parent_id' => null,
                'description' => $parent['description'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $ngoaiViId = DB::table('categories')
            ->where('slug', 'thiet-bi-ngoai-vi')
            ->value('id');

        $cryptoId = DB::table('categories')
            ->where('slug', 'thiet-bi-crypto')
            ->value('id');

        $phuKienId = DB::table('categories')
            ->where('slug', 'phu-kien-cong-nghe')
            ->value('id');

        // Danh mục con
        $children = [
            [
                'name' => 'Chuột',
                'slug' => 'chuot',
                'parent_id' => $ngoaiViId,
                'description' => 'Chuột văn phòng, chuột gaming, chuột không dây',
            ],
            [
                'name' => 'Tai Nghe',
                'slug' => 'tai-nghe',
                'parent_id' => $ngoaiViId,
                'description' => 'Tai nghe gaming, tai nghe không dây, tai nghe chống ồn',
            ],
            [
                'name' => 'Bàn Phím',
                'slug' => 'ban-phim',
                'parent_id' => $ngoaiViId,
                'description' => 'Bàn phím cơ, bàn phím không dây, bàn phím gaming',
            ],
            [
                'name' => 'Ví Lạnh',
                'slug' => 'vi-lanh',
                'parent_id' => $cryptoId,
                'description' => 'Ví lạnh Ledger, Trezor, SafePal dùng lưu trữ tiền điện tử',
            ],
            [
                'name' => 'Seed Backup',
                'slug' => 'seed-backup',
                'parent_id' => $cryptoId,
                'description' => 'Thiết bị lưu trữ seed phrase bằng kim loại',
            ],
            [
                'name' => 'Security Key',
                'slug' => 'security-key',
                'parent_id' => $cryptoId,
                'description' => 'Khóa bảo mật đăng nhập hai lớp như YubiKey',
            ],
            [
                'name' => 'Cáp Kết Nối',
                'slug' => 'cap-ket-noi',
                'parent_id' => $phuKienId,
                'description' => 'Cáp USB-A, USB-C, cáp sạc và truyền dữ liệu',
            ],
        ];

        foreach ($children as $child) {
            DB::table('categories')->insert([
                'name' => $child['name'],
                'slug' => $child['slug'],
                'parent_id' => $child['parent_id'],
                'description' => $child['description'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
