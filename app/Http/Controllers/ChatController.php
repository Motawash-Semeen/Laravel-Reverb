<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Show the chat page.
     */
    public function index()
    {
        $messages = Message::with('user')
            ->latest('id')
            ->take(50)
            ->get()
            ->sortBy('id')
            ->values();

        return view('chat', compact('messages'));
    }

    /**
     * Send a new message.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        $message->load('user');

        broadcast(new MessageSent(Auth::user(), $message));

        return response()->json([
            'status' => 'Message sent!',
            'message' => $message,
        ]);
    }
}
