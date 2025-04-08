<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Kiểm tra xem bảng chat_group_users có tồn tại hay không
        if (Schema::hasTable('chat_group_users')) {
            // Kiểm tra xem bảng chat_group_members đã tồn tại chưa
            if (!Schema::hasTable('chat_group_members')) {
                // Tạo bảng chat_group_members
                Schema::create('chat_group_members', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('group_id')->constrained('chat_groups')->onDelete('cascade');
                    $table->foreignId('user_id')->constrained()->onDelete('cascade');
                    $table->timestamp('joined_at')->useCurrent();
                    $table->timestamps();
                    
                    // Tạo khóa duy nhất để ngăn thành viên trùng lặp
                    $table->unique(['group_id', 'user_id']);
                });
            }
            
            // Đảm bảo rằng bảng chat_group_members có cột group_id
            if (!Schema::hasColumn('chat_group_members', 'group_id') && Schema::hasColumn('chat_group_users', 'chat_group_id')) {
                // Sao chép dữ liệu từ chat_group_users sang chat_group_members
                $users = DB::table('chat_group_users')->get();
                foreach ($users as $user) {
                    // Chỉ chèn nếu thành viên không tồn tại trong bảng chat_group_members
                    $existingMember = DB::table('chat_group_members')
                        ->where('group_id', $user->chat_group_id)
                        ->where('user_id', $user->user_id)
                        ->first();
                        
                    if (!$existingMember) {
                        DB::table('chat_group_members')->insert([
                            'group_id' => $user->chat_group_id,
                            'user_id' => $user->user_id,
                            'joined_at' => $user->joined_at,
                            'created_at' => $user->created_at,
                            'updated_at' => $user->updated_at,
                        ]);
                    }
                }
            }
        } 
        // Nếu không có bảng chat_group_users nhưng không có bảng chat_group_members
        else if (!Schema::hasTable('chat_group_members')) {
            // Tạo bảng chat_group_members
            Schema::create('chat_group_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('group_id')->constrained('chat_groups')->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->timestamp('joined_at')->useCurrent();
                $table->timestamps();
                
                // Tạo khóa duy nhất để ngăn thành viên trùng lặp
                $table->unique(['group_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không thực hiện gì trong phương thức down vì chúng ta không muốn mất dữ liệu
    }
};
