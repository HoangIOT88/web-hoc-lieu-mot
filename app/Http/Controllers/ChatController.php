<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatGroup;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
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
        $userGroups = Auth::user()->chatGroups()->paginate(10);
        return view('chat.index', compact('userGroups'));
    }
    
    /**
     * Display the specified chat group with messages.
     *
     * @param  \App\Models\ChatGroup  $chatGroup
     * @return \Illuminate\Http\Response
     */
    public function show(ChatGroup $chatGroup)
    {
        // Check if user is a member of this chat group
        $isMember = $chatGroup->members()->where('user_id', Auth::id())->exists();
        
        if (!$isMember && !Auth::user()->isAdmin()) {
            return redirect()->route('chat-groups.index')->with('error', 'You are not a member of this chat group.');
        }
        
        $messages = $chatGroup->messages()->with('sender')->orderBy('sent_at', 'asc')->get();
        
        return view('chat.show', compact('chatGroup', 'messages'));
    }
    
    /**
     * Send a message to a chat group.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ChatGroup  $chatGroup
     * @return \Illuminate\Http\Response
     */
    public function sendMessage(Request $request, ChatGroup $chatGroup)
    {
        // Check if user is a member of this chat group
        $isMember = $chatGroup->members()->where('user_id', Auth::id())->exists();
        
        if (!$isMember && !Auth::user()->isAdmin()) {
            return redirect()->route('chat-groups.index')->with('error', 'You are not a member of this chat group.');
        }
        
        // Validate the message
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);
        
        // Create message
        Message::create([
            'chat_group_id' => $chatGroup->id,
            'sender_id' => Auth::id(),
            'content' => $request->content,
            'sent_at' => now(),
        ]);
        
        return redirect()->route('chat-groups.show', $chatGroup)->with('success', 'Message sent successfully.');
    }
    
    /**
     * Join a chat group.
     *
     * @param  \App\Models\ChatGroup  $chatGroup
     * @return \Illuminate\Http\Response
     */
    public function join(ChatGroup $chatGroup)
    {
        // Check if already a member
        $isMember = $chatGroup->members()->where('user_id', Auth::id())->exists();
        
        if ($isMember) {
            return redirect()->route('chat-groups.show', $chatGroup)->with('info', 'You are already a member of this chat group.');
        }
        
        // Add user to group
        $chatGroup->members()->attach(Auth::id(), ['joined_at' => now()]);
        
        return redirect()->route('chat-groups.show', $chatGroup)->with('success', 'You have joined the chat group.');
    }
    
    /**
     * Leave a chat group.
     *
     * @param  \App\Models\ChatGroup  $chatGroup
     * @return \Illuminate\Http\Response
     */
    public function leave(ChatGroup $chatGroup)
    {
        // Check if a member
        $isMember = $chatGroup->members()->where('user_id', Auth::id())->exists();
        
        if (!$isMember) {
            return redirect()->route('chat-groups.index')->with('error', 'You are not a member of this chat group.');
        }
        
        // Remove user from group
        $chatGroup->members()->detach(Auth::id());
        
        return redirect()->route('chat-groups.index')->with('success', 'You have left the chat group.');
    }
}
