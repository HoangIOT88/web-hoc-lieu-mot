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
        if (!Schema::hasTable('chat_group_members')) {
            Schema::create('chat_group_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('chat_group_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->timestamp('joined_at');
                $table->timestamps();
                
                // Đảm bảo rằng một người dùng chỉ có thể là thành viên của một nhóm một lần
                $table->unique(['chat_group_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_group_members');
    }
};
