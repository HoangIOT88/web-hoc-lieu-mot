<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Đổi tên bảng từ chat_group_users thành chat_group_members
        if (Schema::hasTable('chat_group_users')) {
            Schema::rename('chat_group_users', 'chat_group_members');

            // Đổi tên cột chat_group_id thành group_id
            Schema::table('chat_group_members', function (Blueprint $table) {
                $table->renameColumn('chat_group_id', 'group_id');
            });
        } else {
            // Nếu bảng chat_group_users không tồn tại, tạo bảng chat_group_members
            Schema::create('chat_group_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('group_id')->constrained('chat_groups')->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->timestamp('joined_at')->useCurrent();
                $table->timestamps();
                
                // Create unique key to prevent duplicate members
                $table->unique(['group_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Đổi tên lại từ chat_group_members về chat_group_users
        if (Schema::hasTable('chat_group_members')) {
            // Đổi tên cột group_id lại thành chat_group_id
            Schema::table('chat_group_members', function (Blueprint $table) {
                $table->renameColumn('group_id', 'chat_group_id');
            });
            
            Schema::rename('chat_group_members', 'chat_group_users');
        }
    }
};
