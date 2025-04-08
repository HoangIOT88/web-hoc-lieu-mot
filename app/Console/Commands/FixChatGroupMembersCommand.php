<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixChatGroupMembersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:fix-members';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sửa lỗi cấu trúc bảng chat_group_members và đồng bộ dữ liệu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Đang kiểm tra cấu trúc bảng chat_group_members...');
        
        // Kiểm tra xem bảng chat_group_members đã tồn tại chưa
        if (!Schema::hasTable('chat_group_members')) {
            $this->info('Bảng chat_group_members không tồn tại. Đang tạo bảng...');
            
            Schema::create('chat_group_members', function ($table) {
                $table->id();
                $table->foreignId('group_id')->constrained('chat_groups')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->timestamp('joined_at')->useCurrent();
                $table->timestamps();
                
                // Tạo khóa duy nhất để ngăn thành viên trùng lặp
                $table->unique(['group_id', 'user_id']);
            });
            
            $this->info('Đã tạo bảng chat_group_members thành công.');
        } else {
            $this->info('Bảng chat_group_members đã tồn tại.');
            
            // Kiểm tra xem có cột group_id không
            if (!Schema::hasColumn('chat_group_members', 'group_id')) {
                $this->info('Cột group_id không tồn tại. Đang thêm cột...');
                
                // Thêm cột group_id
                Schema::table('chat_group_members', function ($table) {
                    $table->foreignId('group_id')->constrained('chat_groups')->onDelete('cascade');
                });
                
                $this->info('Đã thêm cột group_id thành công.');
            } else {
                $this->info('Cột group_id đã tồn tại.');
            }
        }
        
        // Kiểm tra xem bảng chat_group_users có tồn tại không
        if (Schema::hasTable('chat_group_users')) {
            $this->info('Bảng chat_group_users tồn tại. Đang sao chép dữ liệu...');
            
            // Đếm số lượng bản ghi trong chat_group_users
            $countUsers = DB::table('chat_group_users')->count();
            $this->info("Số lượng bản ghi trong chat_group_users: {$countUsers}");
            
            // Sao chép dữ liệu từ chat_group_users sang chat_group_members
            $chatGroupUsers = DB::table('chat_group_users')->get();
            
            $this->output->progressStart(count($chatGroupUsers));
            
            $newRecords = 0;
            $duplicates = 0;
            
            foreach ($chatGroupUsers as $user) {
                // Kiểm tra xem bản ghi đã tồn tại chưa
                $exists = DB::table('chat_group_members')
                    ->where('group_id', $user->chat_group_id)
                    ->where('user_id', $user->user_id)
                    ->exists();
                
                if (!$exists) {
                    try {
                        // Thêm bản ghi mới
                        DB::table('chat_group_members')->insert([
                            'group_id' => $user->chat_group_id,
                            'user_id' => $user->user_id,
                            'joined_at' => $user->joined_at,
                            'created_at' => $user->created_at,
                            'updated_at' => $user->updated_at,
                        ]);
                        
                        $newRecords++;
                    } catch (\Exception $e) {
                        $this->error("Lỗi khi thêm bản ghi: {$e->getMessage()}");
                    }
                } else {
                    $duplicates++;
                }
                
                $this->output->progressAdvance();
            }
            
            $this->output->progressFinish();
            
            $this->info("Đã sao chép {$newRecords} bản ghi từ chat_group_users.");
            $this->info("Bỏ qua {$duplicates} bản ghi trùng lặp.");
        } else {
            $this->info('Bảng chat_group_users không tồn tại.');
        }
        
        // Hiển thị thông tin về bảng chat_group_members
        $countMembers = DB::table('chat_group_members')->count();
        $this->info("Số lượng bản ghi trong chat_group_members: {$countMembers}");
        
        // Hiển thị thông tin chi tiết để debug
        $groupsInfo = DB::table('chat_groups')
            ->leftJoin('chat_group_members', 'chat_groups.id', '=', 'chat_group_members.group_id')
            ->select('chat_groups.id', 'chat_groups.name', DB::raw('count(chat_group_members.id) as member_count'))
            ->groupBy('chat_groups.id', 'chat_groups.name')
            ->get();
        
        $this->info("\nThông tin chi tiết về các nhóm chat:");
        
        $tableData = [];
        foreach ($groupsInfo as $group) {
            $tableData[] = [
                $group->id,
                $group->name,
                $group->member_count
            ];
        }
        
        $this->table(
            ['ID', 'Tên nhóm', 'Số lượng thành viên'],
            $tableData
        );
        
        $this->info('Hoàn tất.');
        
        return Command::SUCCESS;
    }
}
