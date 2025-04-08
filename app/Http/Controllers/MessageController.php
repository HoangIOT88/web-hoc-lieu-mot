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
        \Log::info('Message store method called', [
            'chatGroup' => $chatGroup->id,
            'user' => Auth::id(),
            'request' => $request->all()
        ]);
        
        // Check if the user is a member of this group, an admin, or a content user
        if (!$chatGroup->members->contains(Auth::id()) && !Auth::user()->isAdmin() && !Auth::user()->isContentUser()) {
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
        
        // Load the sender relationship
        $message->load('sender');
        
        // If AJAX request
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => [
                    'id' => $message->id,
                    'content' => $message->content,
                    'sender_id' => $message->sender_id,
                    'sent_at' => $message->sent_at->format('Y-m-d H:i:s'),
                ],
                'sender' => [
                    'id' => Auth::id(),
                    'name' => Auth::user()->name,
                ],
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
        \Log::info('Getting messages for group', ['chatGroup' => $chatGroup->id]);
        
        // Check if the user is a member of this group
        if (!$chatGroup->members->contains(Auth::id()) && !Auth::user()->isAdmin()) {
            return response()->json(['error' => 'You are not a member of this chat group.'], 403);
        }
        
        $messages = $chatGroup->messages()
            ->with('sender:id,name')
            ->latest('sent_at')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();
            
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