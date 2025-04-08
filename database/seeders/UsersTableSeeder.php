<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kiểm tra xem các role được định nghĩa như thế nào trong User model
        $adminRole = defined('App\Models\User::ROLE_ADMIN') ? User::ROLE_ADMIN : 'admin';
        $contentUserRole = defined('App\Models\User::ROLE_CONTENT_USER') ? User::ROLE_CONTENT_USER : 'content_user';
        $userRole = defined('App\Models\User::ROLE_USER') ? User::ROLE_USER : 'user';
        
        // Admin User
        User::firstOrCreate(
            ['email' => 'admintest@example.com'],
            [
                'name' => 'Admin Test',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
            ]
        );

        // Content Users
        User::firstOrCreate(
            ['email' => 'content1test@example.com'],
            [
                'name' => 'Content Manager Test 1',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CONTENT_USER,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'content2test@example.com'],
            [
                'name' => 'Content Manager Test 2',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CONTENT_USER,
                'email_verified_at' => now(),
            ]
        );

        // Regular Users
        User::firstOrCreate(
            ['email' => 'student1test@example.com'],
            [
                'name' => 'Student Test 1',
                'password' => Hash::make('password'),
                'role' => User::ROLE_USER,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'student2test@example.com'],
            [
                'name' => 'Student Test 2',
                'password' => Hash::make('password'),
                'role' => User::ROLE_USER,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'student3test@example.com'],
            [
                'name' => 'Student Test 3',
                'password' => Hash::make('password'),
                'role' => User::ROLE_USER,
                'email_verified_at' => now(),
            ]
        );

        // Thông báo
        $this->command->info('Đã cập nhật tài khoản người dùng:');
        $this->command->info('1 Admin: admintest@example.com');
        $this->command->info('2 Content Users: content1test@example.com, content2test@example.com');
        $this->command->info('3 Regular Users: student1test@example.com, student2test@example.com, student3test@example.com');
        $this->command->info('Mật khẩu cho tất cả các tài khoản là: password');
    }
}
