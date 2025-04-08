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
        // Drop all existing tables that we're going to create
        Schema::dropIfExists('admin_reports');
        Schema::dropIfExists('user_approvals');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('chat_group_members');
        Schema::dropIfExists('chat_groups');
        Schema::dropIfExists('exercise_submissions');
        Schema::dropIfExists('exercises');
        Schema::dropIfExists('lectures');
        Schema::dropIfExists('course_registrations');
        Schema::dropIfExists('courses');
        
        // We'll keep the default users table (created in 2014_10_12_000000_create_users_table.php)
        // but make sure it's modified to include our role column
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['USER', 'CONTENT_USER', 'ADMIN'])->default('USER')->after('password');
            });
        }

        // Create courses table
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('duration');
            $table->foreignId('content_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Create course_registrations table
        Schema::create('course_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamps();
        });

        // Create lectures table
        Schema::create('lectures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_url')->nullable();
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();
        });

        // Create exercises table
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->timestamp('deadline')->nullable();
            $table->timestamps();
        });

        // Create exercise_submissions table
        Schema::create('exercise_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exercise_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('submission_content');
            $table->timestamp('submitted_at')->useCurrent();
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();
        });

        // Create chat_groups table
        Schema::create('chat_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Create chat_group_members table
        Schema::create('chat_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('chat_groups')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();
        });

        // Create messages table
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('chat_groups')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamps();
        });

        // Create user_approvals table
        Schema::create('user_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('content_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        // Create admin_reports table
        Schema::create('admin_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('generated_by_admin_id')->constrained('users')->onDelete('cascade');
            $table->enum('report_type', ['USER_STATS', 'COURSE_STATS', 'ACTIVITY_STATS']);
            $table->string('file_url');
            $table->timestamp('generated_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop all tables in reverse order
        Schema::dropIfExists('admin_reports');
        Schema::dropIfExists('user_approvals');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('chat_group_members');
        Schema::dropIfExists('chat_groups');
        Schema::dropIfExists('exercise_submissions');
        Schema::dropIfExists('exercises');
        Schema::dropIfExists('lectures');
        Schema::dropIfExists('course_registrations');
        Schema::dropIfExists('courses');
        
        // Remove the role column from users table if we added it
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};
