<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $users = [
            [
                'code'       => 'ADMIN00001',
                'full_name'  => 'Quản Trị Viên',
                'email'      => 'admin@shopnline.vn',
                'password'   => Hash::make('Admin@123'),
                'phone'      => '0901000001',
                'role'       => 1, // admin
                'status'     => 1,
            ],
            [
                'code'       => 'USER000001',
                'full_name'  => 'Nguyễn Văn An',
                'email'      => 'an.nguyen@gmail.com',
                'password'   => Hash::make('User@123'),
                'phone'      => '0901000002',
                'role'       => 0,
                'status'     => 1,
            ],
            [
                'code'       => 'USER000002',
                'full_name'  => 'Trần Thị Bình',
                'email'      => 'binh.tran@gmail.com',
                'password'   => Hash::make('User@123'),
                'phone'      => '0901000003',
                'role'       => 0,
                'status'     => 1,
            ],
            [
                'code'       => 'USER000003',
                'full_name'  => 'Lê Minh Cường',
                'email'      => 'cuong.le@gmail.com',
                'password'   => Hash::make('User@123'),
                'phone'      => '0901000004',
                'role'       => 0,
                'status'     => 1,
            ],
            [
                'code'       => 'USER000004',
                'full_name'  => 'Phạm Thị Dung',
                'email'      => 'dung.pham@gmail.com',
                'password'   => Hash::make('User@123'),
                'phone'      => null,
                'role'       => 0,
                'status'     => 0, // tài khoản bị khóa
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert(array_merge($user, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }
}
