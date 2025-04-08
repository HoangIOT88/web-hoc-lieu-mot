<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Course;
use App\Models\Lecture;
use App\Models\Exercise;
use App\Models\ChatGroup;
use App\Models\ChatGroupMember;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Tạo người dùng admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'ADMIN',
                'email_verified_at' => now(),
            ]
        );
        
        // Tạo người dùng content
        $contentUser = User::firstOrCreate(
            ['email' => 'content@example.com'],
            [
                'name' => 'Content User',
                'email' => 'content@example.com',
                'password' => Hash::make('password'),
                'role' => 'CONTENT_USER',
                'email_verified_at' => now(),
            ]
        );
        
        // Tạo người dùng thường
        $regularUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'role' => 'USER',
                'email_verified_at' => now(),
            ]
        );
        
        // Thông báo tạo người dùng thành công
        echo "Các tài khoản mẫu đã được tạo:\n";
        echo "Admin: admin@example.com / password\n";
        echo "Content: content@example.com / password\n";
        echo "User: user@example.com / password\n";
    }
}
