<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     *
     * @param Message $message
     * @return void
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channelName = 'chat-group.' . $this->message->chat_group_id;
        
        \Log::info('NewMessageEvent - Broadcasting on channel', [
            'channel' => $channelName,
            'message_id' => $this->message->id,
            'sender' => $this->message->sender->name,
        ]);
        
        return [
            new Channel($channelName),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'message.new';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        // Load sender if not already loaded
        if (!$this->message->relationLoaded('sender')) {
            $this->message->load('sender');
        }
        
        \Log::info('Broadcasting message data', [
            'message_id' => $this->message->id,
            'content' => $this->message->content,
            'sender' => $this->message->sender->name,
        ]);
        
        return [
            'id' => $this->message->id,
            'content' => $this->message->content,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'sent_at' => $this->message->sent_at->format('h:i A'),
            'chat_group_id' => $this->message->chat_group_id,
            'timestamp' => time(),
        ];
    }
}
