<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Tự động tạo lại tài khoản khoản Admin chuẩn
        User::updateOrCreate(
            ['email' => 'admin@motorbike.com'], // Giữ đúng email bạn đang đăng nhập
            [
                'name' => 'Quản trị viên',
                'password' => Hash::make('admin123'), // Mật khẩu thiết lập: admin123
                'phone' => '0999999999',
                'address' => 'Hà Nội',
                'role' => 'admin', // Đảm bảo cột phân quyền trùng với cấu trúc đồ án của bạn
            ]
        );

        // 2. Gọi ProductSeeder để nạp tiếp 35 xe máy có ảnh
        $this->call([
            ProductSeeder::class,
        ]);
    }
}