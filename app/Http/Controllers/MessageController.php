<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
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
     * Store a newly created message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ChatGroup  $chatGroup
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, ChatGroup $chatGroup)
    {
        // Check if the user is a member of this group
        if (!$chatGroup->members->contains(Auth::id())) {
            return response()->json(['error' => 'You are not a member of this chat group.'], 403);
        }
        
        $request->validate([
            'content' => 'required|string',
        ]);
        
        $message = Message::create([
            'group_id' => $chatGroup->id,
            'sender_id' => Auth::id(),
            'content' => $request->content,
            'sent_at' => now(),
        ]);
        
        // If AJAX request
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'sender' => Auth::user()->name,
                'sent_at' => $message->sent_at->format('M d, Y H:i'),
            ]);
        }
        
        return redirect()->route('chat-groups.show', $chatGroup)
            ->with('success', 'Message sent successfully.');
    }
    
    /**
     * Get all messages for a chat group.
     *
     * @param  \App\Models\ChatGroup  $chatGroup
     * @return \Illuminate\Http\Response
     */
    public function getMessages(ChatGroup $chatGroup)
    {
        // Check if the user is a member of this group
        if (!$chatGroup->members->contains(Auth::id()) && !Auth::user()->isAdmin()) {
            return response()->json(['error' => 'You are not a member of this chat group.'], 403);
        }
        
        $messages = $chatGroup->messages()
            ->with('sender:id,name')
            ->latest()
            ->paginate(50);
            
        return response()->json($messages);
    }
    
    /**
     * Get new messages for a chat group since last message ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ChatGroup  $chatGroup
     * @return \Illuminate\Http\Response
     */
    public function getNewMessages(Request $request, ChatGroup $chatGroup)
    {
        // Check if the user is a member of this group
        if (!$chatGroup->members->contains(Auth::id()) && !Auth::user()->isAdmin()) {
            return response()->json(['error' => 'You are not a member of this chat group.'], 403);
        }
        
        $request->validate([
            'last_id' => 'required|integer',
        ]);
        
        $messages = $chatGroup->messages()
            ->with('sender:id,name')
            ->where('id', '>', $request->last_id)
            ->orderBy('id')
            ->get();
            
        return response()->json($messages);
    }
    
    /**
     * Delete a message.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        // Check if the user is the sender or an admin
        if ($message->sender_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return response()->json(['error' => 'You do not have permission to delete this message.'], 403);
        }
        
        $message->delete();
        
        return response()->json(['success' => true]);
    }
} 