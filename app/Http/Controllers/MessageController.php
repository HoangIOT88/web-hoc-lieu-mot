<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Events\NewMessageEvent;

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
            'content' => $request->content ? substr($request->content, 0, 50) . '...' : null
        ]);
        
        // Kiểm tra quyền
        $isMember = DB::table('chat_group_members')
            ->where('chat_group_id', $chatGroup->id)
            ->where('user_id', Auth::id())
            ->exists();
        
        if (!$isMember && !Auth::user()->isAdmin() && !Auth::user()->isContentUser()) {
            return response()->json(['error' => 'You are not a member of this chat group.'], 403);
        }
        
        $request->validate([
            'content' => 'required|string',
        ]);
        
        // Tạo tin nhắn mới
        $message = Message::create([
            'chat_group_id' => $chatGroup->id,
            'sender_id' => Auth::id(),
            'content' => $request->content,
            'sent_at' => now(),
        ]);
        
        // Load relationship
        $message->load('sender');
        
        try {
            \Log::info('Broadcasting new message event', [
                'message_id' => $message->id, 
                'sender' => Auth::user()->name
            ]);
            
            // Broadcast sự kiện ngay lập tức
            broadcast(new NewMessageEvent($message))->toOthers();
            
            \Log::info('Message broadcast completed', ['id' => $message->id]);
        } catch (\Exception $e) {
            \Log::error('Failed to broadcast message', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        // Trả về response
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => [
                    'id' => $message->id,
                    'content' => $message->content,
                    'sender_id' => $message->sender_id,
                    'sent_at' => $message->sent_at->format('h:i A'),
                ],
                'sender' => [
                    'id' => Auth::id(),
                    'name' => Auth::user()->name,
                ],
                'status' => 'success'
            ]);
        }
        
        return redirect()->route('chat-groups.show', $chatGroup)
            ->with('success', 'Tin nhắn đã được gửi thành công.');
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
        
        // Kiểm tra sử dụng trực tiếp SQL thay vì qua relationship
        $isMember = DB::table('chat_group_members')
            ->where('chat_group_id', $chatGroup->id)
            ->where('user_id', Auth::id())
            ->exists();
        
        // Check if the user is a member of this group
        if (!$isMember && !Auth::user()->isAdmin()) {
            return response()->json(['error' => 'You are not a member of this chat group.'], 403);
        }
        
        $messages = $chatGroup->messages()
            ->with('sender:id,name')
            ->orderBy('sent_at', 'asc')
            ->limit(50)
            ->get();
            
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
        // Kiểm tra sử dụng trực tiếp SQL thay vì qua relationship
        $isMember = DB::table('chat_group_members')
            ->where('chat_group_id', $chatGroup->id)
            ->where('user_id', Auth::id())
            ->exists();
        
        // Check if the user is a member of this group
        if (!$isMember && !Auth::user()->isAdmin()) {
            return response()->json(['error' => 'You are not a member of this chat group.'], 403);
        }
        
        $request->validate([
            'last_id' => 'required|integer',
        ]);
        
        $messages = $chatGroup->messages()
            ->with('sender:id,name')
            ->where('id', '>', $request->last_id)
            ->orderBy('sent_at', 'asc')
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
    
    /**
     * Broadcast that the authenticated user is typing in a chat group.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ChatGroup  $chatGroup
     * @return \Illuminate\Http\Response
     */
    public function typing(Request $request, ChatGroup $chatGroup)
    {
        // Kiểm tra sử dụng trực tiếp SQL thay vì qua relationship
        $isMember = DB::table('chat_group_members')
            ->where('chat_group_id', $chatGroup->id)
            ->where('user_id', Auth::id())
            ->exists();
        
        // Check if the user is a member of this group
        if (!$isMember && !Auth::user()->isAdmin() && !Auth::user()->isContentUser()) {
            return response()->json(['error' => 'You are not a member of this chat group.'], 403);
        }
        
        // Broadcast event to channel
        broadcast(new \App\Events\UserTypingEvent($chatGroup->id, Auth::id(), Auth::user()->name))->toOthers();
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Broadcast that the authenticated user has stopped typing in a chat group.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ChatGroup  $chatGroup
     * @return \Illuminate\Http\Response
     */
    public function stopTyping(Request $request, ChatGroup $chatGroup)
    {
        // Kiểm tra sử dụng trực tiếp SQL thay vì qua relationship
        $isMember = DB::table('chat_group_members')
            ->where('chat_group_id', $chatGroup->id)
            ->where('user_id', Auth::id())
            ->exists();
        
        // Check if the user is a member of this group
        if (!$isMember && !Auth::user()->isAdmin() && !Auth::user()->isContentUser()) {
            return response()->json(['error' => 'You are not a member of this chat group.'], 403);
        }
        
        // Broadcast event to channel
        broadcast(new \App\Events\UserStopTypingEvent($chatGroup->id, Auth::id(), Auth::user()->name))->toOthers();
        
        return response()->json(['success' => true]);
    }
} 