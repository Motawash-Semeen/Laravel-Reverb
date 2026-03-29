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
    .message-attachment {
        margin-top: 8px;
        padding: 0 8px;
    }
    .message-attachment a {
        color: #00d2ff;
        text-decoration: none;
        font-size: 0.85rem;
    }
    .message-attachment a:hover {
        text-decoration: underline;
    }
    .chat-input-area {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: center;
        gap: 10px;
        padding: 15px 20px;
        background: #0f3460;
        border-radius: 0 0 12px 12px;
    }
    .message-input-wrap {
        position: relative;
        width: 100%;
        min-width: 0;
    }
    .chat-input-area input[type="text"] {
        display: block;
        width: 100%;
        padding: 12px 18px;
        padding-right: 52px;
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
    #send-btn {
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
    #send-btn:hover {
        opacity: 0.9;
        transform: scale(1.02);
    }
    #send-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .attach-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        width: 34px;
        height: 34px;
        border: none;
        border-radius: 999px;
        background: transparent;
        color: #9dc3ff;
        font-size: 1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .attach-icon:hover {
        background: rgba(58, 123, 213, 0.25);
        color: #d8ecff;
    }
    .attach-icon.has-file {
        color: #00d2ff;
        background: rgba(0, 210, 255, 0.12);
    }
    .attachment-name {
        grid-column: 1 / -1;
        font-size: 0.8rem;
        color: #a0a0a0;
        min-height: 16px;
        padding: 0 8px;
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
        <div class="message-wrapper {{ $msg->user_id === Auth::id() ? 'mine' : 'others' }}" data-message-id="{{ $msg->id }}">
            <span class="message-sender">{{ $msg->user->name }}</span>
            @if($msg->message)
            <div class="message-bubble">{{ $msg->message }}</div>
            @endif
            @if($msg->attachment_url)
            <div class="message-attachment">
                <a href="{{ $msg->attachment_url }}" target="_blank" rel="noopener noreferrer">
                    📎 {{ $msg->attachment_name }}
                </a>
            </div>
            @endif
            <span class="message-time" data-timestamp="{{ $msg->created_at->toIso8601String() }}">
                {{ $msg->created_at->format('h:i A') }}
            </span>
        </div>
        @empty
        <div class="no-messages" id="no-messages">No messages yet. Say hello! 👋</div>
        @endforelse
    </div>
    <div class="chat-input-area">
        <div class="message-input-wrap">
            <input type="text" id="message-input" placeholder="Type your message..." autocomplete="off" autofocus>
            <button type="button" id="attach-btn" class="attach-icon" title="Attach file" aria-label="Attach file">📎</button>
        </div>
        <input type="file" id="attachment-input" hidden>
        <button id="send-btn" onclick="sendMessage()">Send</button>
        <div id="attachment-name" class="attachment-name"></div>
    </div>
</div>
@endsection

@section('scripts')
@vite(['resources/js/app.js'])
<script>
    const currentUserId = {{ Auth::id() }};
    const currentUser = {
        id: {{ Auth::id() }},
        name: @json(Auth::user()->name),
    };
    const chatBox = document.getElementById('chat-box');
    const messageInput = document.getElementById('message-input');
    const attachmentInput = document.getElementById('attachment-input');
    const attachBtn = document.getElementById('attach-btn');
    const attachmentName = document.getElementById('attachment-name');
    const sendBtn = document.getElementById('send-btn');
    const connectionStatus = document.getElementById('connection-status');
    const renderedMessageIds = new Set(
        [...document.querySelectorAll('.message-wrapper[data-message-id]')]
            .map((node) => Number(node.dataset.messageId))
            .filter((id) => Number.isFinite(id))
    );

    function formatMessageTime(timestamp) {
        const date = timestamp ? new Date(timestamp) : new Date();

        if (Number.isNaN(date.getTime())) {
            return new Date().toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true,
            });
        }

        return date.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
        });
    }

    // Scroll to bottom on page load
    document.querySelectorAll('.message-time[data-timestamp]').forEach((timeNode) => {
        timeNode.textContent = formatMessageTime(timeNode.dataset.timestamp);
    });

    chatBox.scrollTop = chatBox.scrollHeight;

    attachBtn.addEventListener('click', function() {
        attachmentInput.click();
    });

    attachmentInput.addEventListener('change', function() {
        const hasFile = attachmentInput.files.length > 0;
        attachBtn.classList.toggle('has-file', hasFile);
        attachmentName.textContent = attachmentInput.files.length
            ? `Selected: ${attachmentInput.files[0].name}`
            : '';
    });

    // Send message on Enter key
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    function sendMessage() {
        const message = messageInput.value.trim();
        const attachment = attachmentInput.files[0] ?? null;

        if (!message && !attachment) return;

        sendBtn.disabled = true;

        const formData = new FormData();
        if (message) {
            formData.append('message', message);
        }
        if (attachment) {
            formData.append('attachment', attachment);
        }

        fetch('{{ route("chat.send") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(async (response) => {
            const data = await response.json();

            if (!response.ok) {
                const validationError = data?.errors
                    ? Object.values(data.errors).flat().join('\n')
                    : 'Could not send message.';
                throw new Error(validationError);
            }

            return data;
        })
        .then(data => {
            if (data.message) {
                appendMessage(currentUser, data.message, true);
            }

            messageInput.value = '';
            attachmentInput.value = '';
            attachmentName.textContent = '';
            attachBtn.classList.remove('has-file');
            sendBtn.disabled = false;
            messageInput.focus();
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'Failed to send message.');
            sendBtn.disabled = false;
            messageInput.focus();
        });
    }

    function formatFileSize(bytes) {
        if (!Number.isFinite(Number(bytes)) || Number(bytes) <= 0) {
            return '';
        }

        const units = ['B', 'KB', 'MB', 'GB'];
        let size = Number(bytes);
        let unitIndex = 0;

        while (size >= 1024 && unitIndex < units.length - 1) {
            size /= 1024;
            unitIndex += 1;
        }

        return `${size.toFixed(unitIndex === 0 ? 0 : 1)} ${units[unitIndex]}`;
    }

    function createAttachmentNode(message) {
        if (!message.attachment_url || !message.attachment_name) {
            return null;
        }

        const attachment = document.createElement('div');
        attachment.className = 'message-attachment';

        const link = document.createElement('a');
        link.href = message.attachment_url;
        link.target = '_blank';
        link.rel = 'noopener noreferrer';

        const sizeLabel = formatFileSize(message.attachment_size);
        link.textContent = sizeLabel
            ? `📎 ${message.attachment_name} (${sizeLabel})`
            : `📎 ${message.attachment_name}`;

        attachment.appendChild(link);

        return attachment;
    }

    function updateExistingMessageNode(messageId, message) {
        const existingWrapper = document.querySelector(`.message-wrapper[data-message-id="${messageId}"]`);
        if (!existingWrapper) {
            return;
        }

        if (message.message && !existingWrapper.querySelector('.message-bubble')) {
            const bubble = document.createElement('div');
            bubble.className = 'message-bubble';
            bubble.textContent = message.message;
            const senderNode = existingWrapper.querySelector('.message-sender');
            if (senderNode && senderNode.nextSibling) {
                existingWrapper.insertBefore(bubble, senderNode.nextSibling);
            } else {
                existingWrapper.appendChild(bubble);
            }
        }

        if (!existingWrapper.querySelector('.message-attachment')) {
            const attachmentNode = createAttachmentNode(message);
            if (attachmentNode) {
                const timeNode = existingWrapper.querySelector('.message-time');
                if (timeNode) {
                    existingWrapper.insertBefore(attachmentNode, timeNode);
                } else {
                    existingWrapper.appendChild(attachmentNode);
                }
            }
        }
    }

    function appendMessage(user, message, isMine) {
        const messageId = Number(message.id);
        if (Number.isFinite(messageId) && renderedMessageIds.has(messageId)) {
            updateExistingMessageNode(messageId, message);
            return;
        }

        if (Number.isFinite(messageId)) {
            renderedMessageIds.add(messageId);
        }

        // Remove "no messages" placeholder
        const noMessages = document.getElementById('no-messages');
        if (noMessages) noMessages.remove();

        const wrapper = document.createElement('div');
        wrapper.className = 'message-wrapper ' + (isMine ? 'mine' : 'others');
        if (Number.isFinite(messageId)) {
            wrapper.dataset.messageId = String(messageId);
        }

        const sender = document.createElement('span');
        sender.className = 'message-sender';
        sender.textContent = user.name;
        wrapper.appendChild(sender);

        if (message.message) {
            const bubble = document.createElement('div');
            bubble.className = 'message-bubble';
            bubble.textContent = message.message;
            wrapper.appendChild(bubble);
        }

        const attachmentNode = createAttachmentNode(message);
        if (attachmentNode) {
            wrapper.appendChild(attachmentNode);
        }

        const time = document.createElement('span');
        time.className = 'message-time';
        const timestamp = message.created_at ?? new Date().toISOString();
        time.dataset.timestamp = timestamp;
        time.textContent = formatMessageTime(timestamp);

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
            .listen('.chat.message.sent', (e) => {
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
