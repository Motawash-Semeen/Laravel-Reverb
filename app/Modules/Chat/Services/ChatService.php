<?php

namespace App\Modules\Chat\Services;

use App\Modules\Chat\Models\Message;
use Illuminate\Support\Collection;

class ChatService
{
    /**
     * Get the latest messages for the chat room.
     */
    public function getRecentMessages(int $limit = 50): Collection
    {
        return Message::with('user')
            ->latest('id')
            ->take($limit)
            ->get()
            ->sortBy('id')
            ->values();
    }

    /**
     * Store a new chat message.
     */
    public function storeMessage(int $userId, string $messageText): Message
    {
        $message = Message::create([
            'user_id' => $userId,
            'message' => $messageText,
        ]);

        $message->load('user');

        return $message;
    }
}
