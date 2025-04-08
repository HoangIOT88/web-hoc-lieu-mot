@extends('layouts.app')

@section('title', $chatGroup->name . ' - Chat')

@section('styles')
<style>
    .chat-container {
        height: calc(100vh - 300px);
        min-height: 400px;
    }
    .message-list {
        height: calc(100% - 70px);
        overflow-y: auto;
    }
    .message-input {
        height: 70px;
    }
    .message-bubble {
        max-width: 80%;
        margin-bottom: 10px;
        padding: 10px 15px;
        border-radius: 18px;
    }
    .message-mine {
        background-color: #dcf8c6;
        margin-left: auto;
    }
    .message-other {
        background-color: #f1f0f0;
    }
    .message-time {
        font-size: 0.7rem;
        color: #999;
        margin-top: 5px;
    }
    .chat-sidebar {
        height: calc(100vh - 300px);
        min-height: 400px;
        overflow-y: auto;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">
    <div class="row">
        <!-- Group Info Sidebar -->
        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Group Info</h5>
                    <a href="{{ route('chat-groups.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
                <div class="card-body">
                    <h4 class="card-title">{{ $chatGroup->name }}</h4>
                    
                    <p class="text-muted mb-3">
                        <small>Created by {{ $chatGroup->creator->name }} on {{ $chatGroup->created_at->format('M d, Y') }}</small>
                    </p>
                    
                    @if($chatGroup->description)
                        <div class="mb-3">
                            <h6 class="text-muted">Description</h6>
                            <p>{{ $chatGroup->description }}</p>
                        </div>
                    @endif
                    
                    <div class="mb-0">
                        <h6 class="text-muted">Members ({{ $chatGroup->members->count() }})</h6>
                        <div class="list-group list-group-flush mt-2">
                            @foreach($chatGroup->members as $member)
                                <div class="list-group-item px-0 py-2 d-flex align-items-center border-0">
                                    <div class="avatar bg-light text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                         style="width: 32px; height: 32px;">
                                        <span>{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <span class="d-block">{{ $member->name }}</span>
                                        @if($member->id === $chatGroup->creator_id)
                                            <small class="text-primary">Creator</small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                @if(auth()->id() === $chatGroup->creator_id || auth()->user()->isAdmin())
                <div class="card-footer">
                    <a href="{{ route('chat-groups.edit', $chatGroup->id) }}" class="btn btn-sm btn-outline-primary w-100">
                        <i class="fas fa-cog me-1"></i> Group Settings
                    </a>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Chat Area -->
        <div class="col-md-9">
            <div class="card" style="height: calc(100vh - 140px);">
                <div class="card-header">
                    <h5 class="mb-0">{{ $chatGroup->name }}</h5>
                </div>
                
                <div class="card-body d-flex flex-column p-0">
                    <!-- Messages Container -->
                    <div id="messages-container" class="flex-grow-1 p-3 overflow-auto">
                        <div id="message-list">
                            @forelse($messages as $message)
                                <div class="message-item mb-3 {{ $message->user_id === auth()->id() ? 'text-end' : '' }}">
                                    <div class="d-inline-block message-bubble p-2 px-3 rounded-3 {{ $message->user_id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }}" 
                                         style="max-width: 75%;">
                                        @if($message->user_id !== auth()->id())
                                            <div class="fw-bold mb-1 small">{{ $message->user->name }}</div>
                                        @endif
                                        <div class="message-content">{{ $message->content }}</div>
                                        <div class="message-time small {{ $message->user_id === auth()->id() ? 'text-white-50' : 'text-muted' }} mt-1">
                                            {{ $message->created_at->format('h:i A') }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-comments fa-3x mb-3"></i>
                                    <p>No messages yet. Start the conversation!</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    
                    <!-- Message Input -->
                    <div class="p-3 border-top">
                        <form id="messageForm">
                            @csrf
                            <input type="hidden" name="chat_group_id" value="{{ $chatGroup->id }}">
                            <div class="input-group">
                                <textarea id="messageContent" name="content" class="form-control" placeholder="Type your message..." rows="1"></textarea>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const messagesContainer = document.getElementById('messages-container');
        const messageList = document.getElementById('message-list');
        const messageForm = document.getElementById('messageForm');
        const messageContent = document.getElementById('messageContent');
        
        // Auto-resize textarea
        messageContent.addEventListener('input', function() {
            this.style.height = '38px';
            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
        });
        
        // Scroll to bottom of messages
        const scrollToBottom = () => {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        };
        
        // Send message
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!messageContent.value.trim()) {
                return;
            }
            
            const formData = new FormData(this);
            
            fetch('/chat/messages', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear input
                    messageContent.value = '';
                    messageContent.style.height = '38px';
                    
                    // Add message to UI
                    const messageItem = document.createElement('div');
                    messageItem.className = 'message-item mb-3 text-end';
                    
                    const time = new Date();
                    const formattedTime = time.toLocaleString('en-US', { 
                        hour: 'numeric', 
                        minute: 'numeric', 
                        hour12: true 
                    });
                    
                    messageItem.innerHTML = `
                        <div class="d-inline-block message-bubble p-2 px-3 rounded-3 bg-primary text-white" style="max-width: 75%;">
                            <div class="message-content">${data.message.content}</div>
                            <div class="message-time small text-white-50 mt-1">
                                ${formattedTime}
                            </div>
                        </div>
                    `;
                    
                    messageList.appendChild(messageItem);
                    scrollToBottom();
                    
                    // If this is the first message, remove the empty state
                    const emptyState = messageList.querySelector('.text-center.py-5');
                    if (emptyState) {
                        emptyState.remove();
                    }
                }
            })
            .catch(error => {
                console.error('Error sending message:', error);
            });
        });
        
        // Initial scroll to bottom
        scrollToBottom();
        
        // Auto refresh messages every 5 seconds
        const refreshMessages = () => {
            fetch(`/api/chat/${{{ $chatGroup->id }}}/messages?since=${getLastMessageTimestamp()}`)
                .then(response => response.json())
                .then(data => {
                    if (data.messages && data.messages.length > 0) {
                        // Add new messages
                        data.messages.forEach(message => {
                            const messageItem = document.createElement('div');
                            messageItem.className = `message-item mb-3 ${message.user_id == {{ auth()->id() }} ? 'text-end' : ''}`;
                            
                            messageItem.innerHTML = `
                                <div class="d-inline-block message-bubble p-2 px-3 rounded-3 ${message.user_id == {{ auth()->id() }} ? 'bg-primary text-white' : 'bg-light'}" 
                                     style="max-width: 75%;">
                                    ${message.user_id != {{ auth()->id() }} ? `<div class="fw-bold mb-1 small">${message.user.name}</div>` : ''}
                                    <div class="message-content">${message.content}</div>
                                    <div class="message-time small ${message.user_id == {{ auth()->id() }} ? 'text-white-50' : 'text-muted'} mt-1">
                                        ${formatTime(message.created_at)}
                                    </div>
                                </div>
                            `;
                            
                            messageList.appendChild(messageItem);
                        });
                        
                        // If these were the first messages, remove the empty state
                        const emptyState = messageList.querySelector('.text-center.py-5');
                        if (emptyState) {
                            emptyState.remove();
                        }
                        
                        scrollToBottom();
                    }
                })
                .catch(error => {
                    console.error('Error refreshing messages:', error);
                });
        };
        
        // Get timestamp of last message for polling
        const getLastMessageTimestamp = () => {
            const messages = document.querySelectorAll('.message-item');
            if (messages.length === 0) return 0;
            
            // In a real app, you would store the actual timestamp with each message
            // This is a simplified approach
            return Math.floor(Date.now() / 1000);
        };
        
        // Format time for display
        const formatTime = (timestamp) => {
            const date = new Date(timestamp);
            return date.toLocaleString('en-US', { 
                hour: 'numeric', 
                minute: 'numeric', 
                hour12: true 
            });
        };
        
        // Set up polling interval
        setInterval(refreshMessages, 5000);
    });
</script>
@endpush 