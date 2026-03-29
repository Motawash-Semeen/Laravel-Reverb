<?php

namespace App\Modules\Chat\Events;

use App\Models\User;
use App\Modules\Chat\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public Message $message;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, Message $message)
    {
        $this->user = $user;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('chat'),
        ];
    }

    /**
     * Use an explicit event name so frontend listeners are stable.
     */
    public function broadcastAs(): string
    {
        return 'chat.message.sent';
    }

    /**
     * Data to broadcast with the event.
     */
    public function broadcastWith(): array
    {
        return [
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'message' => [
                'id' => $this->message->id,
                'message' => $this->message->message,
                'created_at' => $this->message->created_at->toIso8601String(),
                'attachment_name' => $this->message->attachment_name,
                'attachment_mime' => $this->message->attachment_mime,
                'attachment_size' => $this->message->attachment_size,
                'attachment_url' => $this->message->attachment_url,
            ],
        ];
    }
}
