<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatGroup;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

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
        // Get all groups the user belongs to
        $groups = Auth::user()->chatGroups;
        
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
        
        return view('chat.create');
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
        ]);
        
        // Create the chat group
        $chatGroup = ChatGroup::create([
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);
        
        // Add the creator to the group
        $chatGroup->members()->attach(Auth::id(), ['joined_at' => now()]);
        
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
        // Check if the user is a member of this group
        if (!$chatGroup->members->contains(Auth::id()) && !Auth::user()->isAdmin()) {
            return redirect()->route('chat-groups.index')
                ->with('error', 'You are not a member of this chat group.');
        }
        
        // Load messages with sender info
        $messages = $chatGroup->messages()->with('sender')->latest()->paginate(50);
        
        // Get all members of the group
        $members = $chatGroup->members;
        
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
        
        return view('chat.edit', compact('chatGroup'));
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
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);
        
        // Get existing member IDs
        $existingMemberIds = $chatGroup->members()->pluck('users.id')->toArray();
        
        // Filter out users who are already members
        $newMemberIds = array_diff($request->user_ids, $existingMemberIds);
        
        // Add new members
        foreach ($newMemberIds as $userId) {
            $chatGroup->members()->attach($userId, ['joined_at' => now()]);
        }
        
        return redirect()->route('chat-groups.show', $chatGroup)
            ->with('success', 'Members added successfully.');
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
} 