@extends('layouts.app')
@section('title', 'Chat Room')

@section('styles')
<style>
    .chat-container {
        max-width: 900px;
        margin: 20px auto;
        padding: 0 20px;
        display: flex;
        flex-direction: column;
        height: calc(100vh - 90px);
    }
    .chat-box {
        flex: 1;
        background: #16213e;
        border-radius: 12px 12px 0 0;
        padding: 20px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .chat-box::-webkit-scrollbar {
        width: 6px;
    }
    .chat-box::-webkit-scrollbar-track {
        background: #0f3460;
    }
    .chat-box::-webkit-scrollbar-thumb {
        background: #3a7bd5;
        border-radius: 3px;
    }
    .message-wrapper {
        display: flex;
        flex-direction: column;
        max-width: 70%;
    }
    .message-wrapper.mine {
        align-self: flex-end;
        align-items: flex-end;
    }
    .message-wrapper.others {
        align-self: flex-start;
        align-items: flex-start;
    }
    .message-sender {
        font-size: 0.75rem;
        color: #00d2ff;
        margin-bottom: 4px;
        padding: 0 8px;
    }
    .message-bubble {
        padding: 10px 16px;
        border-radius: 16px;
        font-size: 0.95rem;
        line-height: 1.4;
        word-wrap: break-word;
    }
    .mine .message-bubble {
        background: linear-gradient(135deg, #3a7bd5, #00d2ff);
        color: white;
        border-bottom-right-radius: 4px;
    }
    .others .message-bubble {
        background: #0f3460;
        color: #e0e0e0;
        border-bottom-left-radius: 4px;
    }
    .message-time {
        font-size: 0.7rem;
        color: #666;
        margin-top: 3px;
        padding: 0 8px;
    }
    .chat-input-area {
        display: flex;
        gap: 10px;
        padding: 15px 20px;
        background: #0f3460;
        border-radius: 0 0 12px 12px;
    }
    .chat-input-area input {
        flex: 1;
        padding: 12px 18px;
        border: 1px solid #2a3a5c;
        border-radius: 25px;
        background: #16213e;
        color: #e0e0e0;
        font-size: 1rem;
        outline: none;
        transition: border-color 0.3s;
    }
    .chat-input-area input:focus {
        border-color: #00d2ff;
    }
    .chat-input-area button {
        padding: 12px 25px;
        border: none;
        border-radius: 25px;
        background: linear-gradient(135deg, #00d2ff, #3a7bd5);
        color: white;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s;
        white-space: nowrap;
    }
    .chat-input-area button:hover {
        opacity: 0.9;
        transform: scale(1.02);
    }
    .chat-input-area button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .no-messages {
        text-align: center;
        color: #555;
        margin-top: 40%;
        font-size: 1.1rem;
    }
    .connection-status {
        text-align: center;
        padding: 6px;
        font-size: 0.8rem;
        border-radius: 0;
    }
    .connection-status.connected {
        background: rgba(46, 204, 113, 0.15);
        color: #2ecc71;
    }
    .connection-status.disconnected {
        background: rgba(231, 76, 60, 0.15);
        color: #e74c3c;
    }
</style>
@endsection

@section('content')
<div class="chat-container">
    <div id="connection-status" class="connection-status disconnected">
        Connecting to chat server...
    </div>
    <div class="chat-box" id="chat-box">
        @forelse($messages as $msg)
        <div class="message-wrapper {{ $msg->user_id === Auth::id() ? 'mine' : 'others' }}">
            <span class="message-sender">{{ $msg->user->name }}</span>
            <div class="message-bubble">{{ $msg->message }}</div>
            <span class="message-time">{{ $msg->created_at->format('h:i A') }}</span>
        </div>
        @empty
        <div class="no-messages" id="no-messages">No messages yet. Say hello! 👋</div>
        @endforelse
    </div>
    <div class="chat-input-area">
        <input type="text" id="message-input" placeholder="Type your message..." autocomplete="off" autofocus>
        <button id="send-btn" onclick="sendMessage()">Send</button>
    </div>
</div>
@endsection

@section('scripts')
@vite(['resources/js/app.js'])
<script>
    const currentUserId = {{ Auth::id() }};
    const chatBox = document.getElementById('chat-box');
    const messageInput = document.getElementById('message-input');
    const sendBtn = document.getElementById('send-btn');
    const connectionStatus = document.getElementById('connection-status');

    // Scroll to bottom on page load
    chatBox.scrollTop = chatBox.scrollHeight;

    // Send message on Enter key
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    function sendMessage() {
        const message = messageInput.value.trim();
        if (!message) return;

        sendBtn.disabled = true;
        messageInput.value = '';

        fetch('{{ route("chat.send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            sendBtn.disabled = false;
            messageInput.focus();
        })
        .catch(error => {
            console.error('Error:', error);
            sendBtn.disabled = false;
            messageInput.focus();
        });
    }

    function appendMessage(user, message, isMine) {
        // Remove "no messages" placeholder
        const noMessages = document.getElementById('no-messages');
        if (noMessages) noMessages.remove();

        const wrapper = document.createElement('div');
        wrapper.className = 'message-wrapper ' + (isMine ? 'mine' : 'others');

        const sender = document.createElement('span');
        sender.className = 'message-sender';
        sender.textContent = user.name;

        const bubble = document.createElement('div');
        bubble.className = 'message-bubble';
        bubble.textContent = message.message;

        const time = document.createElement('span');
        time.className = 'message-time';
        const now = new Date();
        time.textContent = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });

        wrapper.appendChild(sender);
        wrapper.appendChild(bubble);
        wrapper.appendChild(time);
        chatBox.appendChild(wrapper);

        // Auto-scroll to bottom
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    // Wait for Echo to be available, then listen
    function initEcho() {
        if (typeof window.Echo === 'undefined') {
            setTimeout(initEcho, 100);
            return;
        }

        window.Echo.channel('chat')
            .listen('MessageSent', (e) => {
                appendMessage(e.user, e.message, e.user.id === currentUserId);
            });

        // Update connection status
        window.Echo.connector.pusher.connection.bind('connected', () => {
            connectionStatus.textContent = '🟢 Connected';
            connectionStatus.className = 'connection-status connected';
        });

        window.Echo.connector.pusher.connection.bind('disconnected', () => {
            connectionStatus.textContent = '🔴 Disconnected — Reconnecting...';
            connectionStatus.className = 'connection-status disconnected';
        });

        // Check if already connected
        if (window.Echo.connector.pusher.connection.state === 'connected') {
            connectionStatus.textContent = '🟢 Connected';
            connectionStatus.className = 'connection-status connected';
        }
    }

    initEcho();
</script>
@endsection
