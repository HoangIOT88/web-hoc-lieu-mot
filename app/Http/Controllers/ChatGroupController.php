<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatGroup;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatGroupController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the chat groups.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get all groups the user belongs to with pagination
        $groups = Auth::user()->chatGroups()->paginate(10);
        
        return view('chat.index', compact('groups'));
    }
    
    /**
     * Show the form for creating a new chat group.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check permission (only content users and admins can create groups)
        if (!Auth::user()->isContentUser() && !Auth::user()->isAdmin()) {
            return redirect()->route('chat-groups.index')
                ->with('error', 'You do not have permission to create chat groups.');
        }
        
        // Get all users for member selection
        $users = \App\Models\User::where('id', '!=', Auth::id())->get();
        
        return view('chat.create', compact('users'));
    }
    
    /**
     * Store a newly created chat group.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Check permission (only content users and admins can create groups)
        if (!Auth::user()->isContentUser() && !Auth::user()->isAdmin()) {
            return redirect()->route('chat-groups.index')
                ->with('error', 'You do not have permission to create chat groups.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);
        
        \Log::info('Creating chat group with members', [
            'name' => $request->name,
            'members' => $request->members ?? []
        ]);
        
        // Create the chat group
        $chatGroup = ChatGroup::create([
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);
        
        // Thêm người tạo nhóm vào nhóm chat - trực tiếp thêm vào database để đảm bảo dữ liệu được lưu
        try {
            DB::table('chat_group_members')->insert([
                'group_id' => $chatGroup->id,
                'user_id' => Auth::id(),
                'joined_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            \Log::info('Added creator to group', [
                'group_id' => $chatGroup->id, 
                'user_id' => Auth::id()
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to add creator to group', [
                'error' => $e->getMessage()
            ]);
        }
        
        // Thêm các thành viên được chọn vào nhóm chat
        if ($request->has('members') && is_array($request->members)) {
            foreach ($request->members as $memberId) {
                try {
                    DB::table('chat_group_members')->insert([
                        'group_id' => $chatGroup->id,
                        'user_id' => $memberId,
                        'joined_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    \Log::info('Added member to new group', [
                        'group_id' => $chatGroup->id, 
                        'user_id' => $memberId
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to add member to new group', [
                        'group_id' => $chatGroup->id,
                        'user_id' => $memberId,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        return redirect()->route('chat-groups.show', $chatGroup)
            ->with('success', 'Chat group created successfully.');
    }
    
    /**
     * Display the specified chat group.
     *
     * @param  \App\Models\ChatGroup  $chatGroup
     * @return \Illuminate\Http\Response
     */
    public function show(ChatGroup $chatGroup)
    {
        \Log::info('Showing chat group', ['group_id' => $chatGroup->id]);
        
        // Kiểm tra thành viên hiện tại
        $memberIds = DB::table('chat_group_members')
            ->where('group_id', $chatGroup->id)
            ->pluck('user_id')
            ->toArray();
        
        \Log::info('Current member IDs from direct query', ['member_ids' => $memberIds]);
        
        // Check if the user is a member of this group
        // Sửa để truy cập trực tiếp vào bảng chat_group_members
        $isMember = in_array(Auth::id(), $memberIds);
        
        if (!$isMember && !Auth::user()->isAdmin()) {
            return redirect()->route('chat-groups.index')
                ->with('error', 'You are not a member of this chat group.');
        }
        
        // Load messages with sender info
        $messages = $chatGroup->messages()->with('sender')->latest()->paginate(50);
        
        // Get all members of the group - eager load with users table
        $members = \App\Models\User::whereIn('id', $memberIds)->get();
        
        \Log::info('Members loaded for view', ['count' => $members->count()]);
        
        return view('chat.show', compact('chatGroup', 'messages', 'members'));
    }
    
    /**
     * Show the form for editing the chat group.
     *
     * @param  \App\Models\ChatGroup  $chatGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(ChatGroup $chatGroup)
    {
        // Check if the user is the creator or an admin
        if ($chatGroup->created_by !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->route('chat-groups.index')
                ->with('error', 'You do not have permission to edit this chat group.');
        }
        
        // Get all users for member selection
        $users = \App\Models\User::where('id', '!=', Auth::id())->get();
        
        // Get current member IDs
        $currentMembers = $chatGroup->members->pluck('id')->toArray();
        
        return view('chat.edit', compact('chatGroup', 'users', 'currentMembers'));
    }
    
    /**
     * Update the specified chat group.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ChatGroup  $chatGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ChatGroup $chatGroup)
    {
        // Check if the user is the creator or an admin
        if ($chatGroup->created_by !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->route('chat-groups.index')
                ->with('error', 'You do not have permission to edit this chat group.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $chatGroup->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);
        
        return redirect()->route('chat-groups.show', $chatGroup)
            ->with('success', 'Chat group updated successfully.');
    }
    
    /**
     * Remove the specified chat group.
     *
     * @param  \App\Models\ChatGroup  $chatGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(ChatGroup $chatGroup)
    {
        // Check if the user is the creator or an admin
        if ($chatGroup->created_by !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->route('chat-groups.index')
                ->with('error', 'You do not have permission to delete this chat group.');
        }
        
        // Delete the chat group (and associated messages & members via DB cascade)
        $chatGroup->delete();
        
        return redirect()->route('chat-groups.index')
            ->with('success', 'Chat group deleted successfully.');
    }
    
    /**
     * Add members to a chat group.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ChatGroup  $chatGroup
     * @return \Illuminate\Http\Response
     */
    public function addMembers(Request $request, ChatGroup $chatGroup)
    {
        // Check if the user is the creator or an admin
        if ($chatGroup->created_by !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->route('chat-groups.show', $chatGroup)
                ->with('error', 'You do not have permission to add members to this chat group.');
        }
        
        $request->validate([
            'members' => 'required|array',
            'members.*' => 'exists:users,id',
        ]);
        
        // Ghi log để debug
        \Log::info('Adding members to chat group', [
            'chat_group_id' => $chatGroup->id,
            'request_members' => $request->members
        ]);
        
        // Get existing member IDs - make sure to use with() to avoid n+1 query problem
        $existingMemberIds = $chatGroup->members()->pluck('users.id')->toArray();
        
        \Log::info('Existing members', ['existing_members' => $existingMemberIds]);
        
        // Filter out users who are already members
        $newMemberIds = array_diff($request->members, $existingMemberIds);
        
        \Log::info('New members to add', ['new_members' => $newMemberIds]);
        
        // Add new members - using DB facade for direct SQL insertion để đảm bảo dữ liệu được thêm
        foreach ($newMemberIds as $userId) {
            try {
                // Trực tiếp chèn vào bảng để đảm bảo không có vấn đề với eloquent relationship
                DB::table('chat_group_members')->insert([
                    'group_id' => $chatGroup->id,
                    'user_id' => $userId,
                    'joined_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                \Log::info('Successfully added member', [
                    'group_id' => $chatGroup->id,
                    'user_id' => $userId
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to add member', [
                    'group_id' => $chatGroup->id,
                    'user_id' => $userId,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return redirect()->route('chat-groups.show', $chatGroup)
            ->with('success', 'Thành viên đã được thêm thành công.');
    }
    
    /**
     * Remove a member from a chat group.
     *
     * @param  \App\Models\ChatGroup  $chatGroup
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function removeMember(ChatGroup $chatGroup, $userId)
    {
        // Check if the user is the creator or an admin
        if ($chatGroup->created_by !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->route('chat-groups.show', $chatGroup)
                ->with('error', 'You do not have permission to remove members from this chat group.');
        }
        
        // Cannot remove the creator
        if ($userId == $chatGroup->created_by) {
            return redirect()->route('chat-groups.show', $chatGroup)
                ->with('error', 'Cannot remove the creator of the chat group.');
        }
        
        // Remove the member
        $chatGroup->members()->detach($userId);
        
        return redirect()->route('chat-groups.show', $chatGroup)
            ->with('success', 'Member removed successfully.');
    }
    
    /**
     * Show form to add members to a chat group.
     *
     * @param  \App\Models\ChatGroup  $chatGroup
     * @return \Illuminate\Http\Response
     */
    public function addMembersForm(ChatGroup $chatGroup)
    {
        // Check if the user is the creator or an admin
        if ($chatGroup->created_by !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->route('chat-groups.show', $chatGroup)
                ->with('error', 'You do not have permission to add members to this chat group.');
        }
        
        // Get current members with eager loading to avoid n+1 query problem
        $currentMembers = $chatGroup->members()->pluck('users.id')->toArray();
        
        // Get all users except those who are already members - use whereNotIn for better performance
        $users = \App\Models\User::whereNotIn('id', $currentMembers)->get();
        
        return view('chat.add-members', compact('chatGroup', 'users'));
    }
} 