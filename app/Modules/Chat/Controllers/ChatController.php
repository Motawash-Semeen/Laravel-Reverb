<?php

namespace App\Modules\Chat\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Chat\Events\MessageSent;
use App\Modules\Chat\Requests\SendMessageRequest;
use App\Modules\Chat\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function __construct(
        protected ChatService $chatService
    ) {}

    /**
     * Show the chat page.
     */
    public function index(): View
    {
        $messages = $this->chatService->getRecentMessages();

        return view('chat', compact('messages'));
    }

    /**
     * Send a new message.
     */
    public function sendMessage(SendMessageRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $message = $this->chatService->storeMessage(
            Auth::id(),
            $validated['message'] ?? null,
            $request->file('attachment')
        );

        broadcast(new MessageSent(Auth::user(), $message));

        return response()->json([
            'status' => 'Message sent!',
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'created_at' => $message->created_at->toIso8601String(),
                'attachment_name' => $message->attachment_name,
                'attachment_mime' => $message->attachment_mime,
                'attachment_size' => $message->attachment_size,
                'attachment_url' => $message->attachment_url,
            ],
        ]);
    }
}
