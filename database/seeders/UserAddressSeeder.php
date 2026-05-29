<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserAddressSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $users = DB::table('users')->where('role', 0)->get();

        $addresses = [
            // User 1: Nguyễn Văn An - 2 địa chỉ
            [
                'user_id'        => $users[0]->id,
                'receiver_name'  => 'Nguyễn Văn An',
                'receiver_phone' => '0901000002',
                'province'       => 'TP. Hồ Chí Minh',
                'district'       => 'Quận 1',
                'ward'           => 'Phường Bến Nghé',
                'address_detail' => '123 Nguyễn Huệ, Phường Bến Nghé',
                'is_default'     => true,
            ],
            [
                'user_id'        => $users[0]->id,
                'receiver_name'  => 'Nguyễn Thị Mẹ',
                'receiver_phone' => '0901000010',
                'province'       => 'Cần Thơ',
                'district'       => 'Quận Ninh Kiều',
                'ward'           => 'Phường An Hòa',
                'address_detail' => '456 Trần Hưng Đạo',
                'is_default'     => false,
            ],
            // User 2: Trần Thị Bình
            [
                'user_id'        => $users[1]->id,
                'receiver_name'  => 'Trần Thị Bình',
                'receiver_phone' => '0901000003',
                'province'       => 'Hà Nội',
                'district'       => 'Quận Cầu Giấy',
                'ward'           => 'Phường Dịch Vọng',
                'address_detail' => '789 Trần Duy Hưng',
                'is_default'     => true,
            ],
            // User 3: Lê Minh Cường
            [
                'user_id'        => $users[2]->id,
                'receiver_name'  => 'Lê Minh Cường',
                'receiver_phone' => '0901000004',
                'province'       => 'Đà Nẵng',
                'district'       => 'Quận Hải Châu',
                'ward'           => 'Phường Thạch Thang',
                'address_detail' => '12 Bạch Đằng',
                'is_default'     => true,
            ],
        ];

        foreach ($addresses as $addr) {
            DB::table('user_addresses')->insert(array_merge($addr, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }
}
