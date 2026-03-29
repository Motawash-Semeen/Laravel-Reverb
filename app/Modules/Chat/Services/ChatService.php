<?php

namespace App\Modules\Chat\Services;

use App\Modules\Chat\Models\Message;
use Illuminate\Support\Collection;
use Illuminate\Http\UploadedFile;

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
    public function storeMessage(int $userId, ?string $messageText, ?UploadedFile $attachment = null): Message
    {
        $cleanMessageText = is_string($messageText) ? trim($messageText) : null;
        if ($cleanMessageText === '') {
            $cleanMessageText = null;
        }

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentMime = null;
        $attachmentSize = null;

        if ($attachment) {
            $attachmentPath = $attachment->store('chat-attachments', 'public');
            $attachmentName = $attachment->getClientOriginalName();
            $attachmentMime = $attachment->getClientMimeType();
            $attachmentSize = $attachment->getSize();
        }

        $message = Message::create([
            'user_id' => $userId,
            'message' => $cleanMessageText,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_mime' => $attachmentMime,
            'attachment_size' => $attachmentSize,
        ]);

        $message->load('user');

        return $message;
    }
}
