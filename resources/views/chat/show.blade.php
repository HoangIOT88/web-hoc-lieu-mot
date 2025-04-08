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
                        <h6 class="text-muted">Members ({{ count($members) }})</h6>
                        <div class="list-group list-group-flush mt-2">
                            @foreach($members as $member)
                                <div class="list-group-item px-0 py-2 d-flex align-items-center border-0">
                                    <div class="avatar bg-light text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                         style="width: 32px; height: 32px;">
                                        <span>{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <span class="d-block">{{ $member->name }}</span>
                                        @if($member->id === $chatGroup->created_by)
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
                    <div class="d-grid gap-2">
                        <a href="{{ route('chat-groups.edit', $chatGroup->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-cog me-1"></i> Group Settings
                        </a>
                        <a href="{{ route('chat-groups.members.form', $chatGroup->id) }}" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-user-plus me-1"></i> Thêm thành viên
                        </a>
                    </div>
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
                                <div class="message-item mb-3 {{ $message->sender_id === auth()->id() ? 'text-end' : '' }}">
                                    <div class="d-inline-block message-bubble p-2 px-3 rounded-3 {{ $message->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }}" 
                                         style="max-width: 75%;">
                                        @if($message->sender_id !== auth()->id())
                                            <div class="fw-bold mb-1 small">{{ $message->sender->name }}</div>
                                        @endif
                                        <div class="message-content">{{ $message->content }}</div>
                                        <div class="message-time small {{ $message->sender_id === auth()->id() ? 'text-white-50' : 'text-muted' }} mt-1">
                                            {{ $message->sent_at->format('h:i A') }}
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
                        <form id="messageForm" action="{{ route('messages.store', $chatGroup->id) }}" method="POST">
                            @csrf
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

@section('scripts')
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
            const url = this.getAttribute('action');
            
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
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
                
                // If this is the first message, remove the empty state
                const emptyState = messageList.querySelector('.text-center.py-5');
                if (emptyState) {
                    emptyState.remove();
                }
                
                // Scroll to bottom
                scrollToBottom();
            })
            .catch(error => {
                console.error('Error sending message:', error);
                alert('Không thể gửi tin nhắn. Vui lòng thử lại sau.');
            });
        });
        
        // Initial scroll to bottom
        scrollToBottom();
        
        // Poll for new messages every 5 seconds
        setInterval(() => {
            const url = `/chat-groups/${{{ $chatGroup->id }}}/messages`;
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Messages data:', data); // Để debug
                
                // Check if there are messages (adjust the check based on your actual API response format)
                if (data && data.length > 0) {
                    // Clear existing messages
                    messageList.innerHTML = '';
                    
                    // Add all messages
                    data.forEach(message => {
                        const isMine = message.sender_id === {{ auth()->id() }};
                        
                        const messageItem = document.createElement('div');
                        messageItem.className = `message-item mb-3 ${isMine ? 'text-end' : ''}`;
                        
                        const sentAt = new Date(message.sent_at);
                        const formattedTime = sentAt.toLocaleString('en-US', { 
                            hour: 'numeric', 
                            minute: 'numeric', 
                            hour12: true 
                        });
                        
                        messageItem.innerHTML = `
                            <div class="d-inline-block message-bubble p-2 px-3 rounded-3 ${isMine ? 'bg-primary text-white' : 'bg-light'}" 
                                 style="max-width: 75%;">
                                ${!isMine ? `<div class="fw-bold mb-1 small">${message.sender.name}</div>` : ''}
                                <div class="message-content">${message.content}</div>
                                <div class="message-time small ${isMine ? 'text-white-50' : 'text-muted'} mt-1">
                                    ${formattedTime}
                                </div>
                            </div>
                        `;
                        
                        messageList.appendChild(messageItem);
                    });
                    
                    // Scroll to bottom
                    scrollToBottom();
                }
            })
            .catch(error => {
                console.error('Error fetching messages:', error);
            });
        }, 5000);
    });
</script>
@endsection 