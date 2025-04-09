<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserStopTypingEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat_group_id;
    public $user_id;
    public $user_name;

    /**
     * Create a new event instance.
     *
     * @param int $chatGroupId
     * @param int $userId
     * @param string $userName
     * @return void
     */
    public function __construct($chatGroupId, $userId, $userName)
    {
        $this->chat_group_id = $chatGroupId;
        $this->user_id = $userId;
        $this->user_name = $userName;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channelName = 'chat-group.' . $this->chat_group_id;
        
        \Log::info('UserStopTypingEvent - Broadcasting on channel', [
            'channel' => $channelName,
            'user' => $this->user_name,
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
        return 'user.stop_typing';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'chat_group_id' => $this->chat_group_id,
            'user_id' => $this->user_id,
            'user_name' => $this->user_name,
            'timestamp' => now()->toIso8601String(),
        ];
    }
} 