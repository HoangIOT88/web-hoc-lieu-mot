<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTypingEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $group_id;
    public $user_id;
    public $user_name;

    /**
     * Create a new event instance.
     *
     * @param int $group_id
     * @param int $user_id
     * @param string $user_name
     * @return void
     */
    public function __construct($group_id, $user_id, $user_name)
    {
        $this->group_id = $group_id;
        $this->user_id = $user_id;
        $this->user_name = $user_name;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat-group.' . $this->group_id),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'typing';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'group_id' => $this->group_id,
            'user_id' => $this->user_id,
            'user_name' => $this->user_name,
            'timestamp' => now()->toIso8601String(),
        ];
    }
} 