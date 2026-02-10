<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Requests\Chat\SendMessageRequest;
use App\Services\ChatService;
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
        $message = $this->chatService->storeMessage(
            Auth::id(),
            $request->validated('message')
        );

        broadcast(new MessageSent(Auth::user(), $message));

        return response()->json([
            'status' => 'Message sent!',
            'message' => $message,
        ]);
    }
}
