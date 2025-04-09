@extends('layouts.app')

@section('title', $chatGroup->name . ' - Chat')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<style>
    .chat-container {
        height: calc(100vh - 300px);
        min-height: 400px;
    }
    #messages-container {
        height: calc(100% - 60px);
        max-height: calc(100vh - 230px);
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }
    #message-list {
        width: 100%;
    }
    .message-input {
        height: 60px;
    }
    .message-bubble {
        max-width: 80%;
        margin-bottom: 10px;
        padding: 10px 15px;
        border-radius: 18px;
        word-break: break-word;
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
    
    /* New message animation */
    @keyframes newMessageHighlight {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .message-item.new-message {
        animation: newMessageHighlight 0.5s ease-in-out;
    }
    
    /* Scroll-to-bottom button */
    .scroll-bottom-btn {
        position: absolute;
        bottom: 80px;
        right: 20px;
        z-index: 1000;
        opacity: 0.8;
        transition: opacity 0.3s;
    }
    
    .scroll-bottom-btn:hover {
        opacity: 1;
    }
    
    /* Unread messages indicator */
    .new-messages-indicator {
        background-color: rgba(40, 167, 69, 0.9);
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        position: absolute;
        bottom: 70px;
        left: 50%;
        transform: translateX(-50%);
        display: none;
        z-index: 900;
        cursor: pointer;
    }
    
    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
    }
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    ::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #555;
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
                                <div class="message-item mb-3 {{ $message->sender_id === auth()->id() ? 'text-end' : '' }}" data-message-id="{{ $message->id }}">
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
                    
                    <!-- New messages indicator -->
                    <div id="new-messages-indicator" class="new-messages-indicator">
                        <i class="fas fa-arrow-down me-1"></i> Tin nhắn mới
                    </div>
                    
                    <!-- Message Input -->
                    <div class="p-3 border-top">
                        <div id="typing-indicator" class="small text-muted mb-2" style="height: 18px; display: none;">
                            <span class="typing-name"></span> đang nhập...
                        </div>
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
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    // Debug helpers
    function debugInfo(message, data) {
        console.log('%c' + message, 'background: #0066ff; color: white; padding: 2px 5px; border-radius: 3px;', data || '');
    }
    
    function debugError(message, data) {
        console.error('%c' + message, 'background: #ff0033; color: white; padding: 2px 5px; border-radius: 3px;', data || '');
    }
    
    function debugSuccess(message, data) {
        console.log('%c' + message, 'background: #00cc66; color: white; padding: 2px 5px; border-radius: 3px;', data || '');
    }
    
    // Thiết lập AJAX CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Initialize script variables
    const chatGroupId = {{ $chatGroup->id }};
    const currentUserId = {{ auth()->id() }};
    const pusherKey = '{{ config('broadcasting.connections.pusher.key') }}';
    const pusherCluster = '{{ config('broadcasting.connections.pusher.options.cluster') }}';
    const csrfToken = '{{ csrf_token() }}';
    
    // Chat initialization
    document.addEventListener('DOMContentLoaded', function() {
        debugInfo('Initializing chat interface');
        
        const messageList = document.getElementById('message-list');
        const messagesContainer = document.getElementById('messages-container');
        const newMessagesIndicator = document.getElementById('new-messages-indicator');
        const messageForm = document.getElementById('messageForm');
        const messageContent = document.getElementById('messageContent');
        
        // Biến theo dõi số tin nhắn mới chưa đọc
        let unreadMessagesCount = 0;
        
        // Function to scroll to bottom of messages
        function scrollToBottom() {
            debugInfo('Scrolling to bottom', messagesContainer.scrollHeight);
            messagesContainer.scrollTop = messagesContainer.scrollHeight + 5000;
            
            // Reset unread counter and hide indicator
            unreadMessagesCount = 0;
            newMessagesIndicator.style.display = 'none';
        }
        
        // Scroll to bottom on load
        scrollToBottom();
        
        // Initialize Pusher connection
        if (!pusherKey || !pusherCluster) {
            debugError('Pusher configuration missing', { pusherKey, pusherCluster });
            return;
        }
        
        debugInfo('Initializing Pusher with', { key: pusherKey, cluster: pusherCluster });
        
        // Create Pusher instance
        const pusher = new Pusher(pusherKey, {
            cluster: pusherCluster,
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-Token': csrfToken,
                }
            }
        });
        
        // Set up connection event handlers
        pusher.connection.bind('connected', function() {
            debugSuccess('📡 Connected to Pusher', pusher.connection.socket_id);
            subscribeToChannel();
        });
        
        pusher.connection.bind('disconnected', function() {
            debugError('❌ Disconnected from Pusher');
        });
        
        pusher.connection.bind('error', function(err) {
            debugError('Pusher connection error', err);
        });
        
        // Subscribe to chat channel
        function subscribeToChannel() {
            const channelName = 'chat-group.' + chatGroupId;
            debugInfo('Subscribing to channel', channelName);
            
            const channel = pusher.subscribe(channelName);
            
            // Handle subscription success
            channel.bind('pusher:subscription_succeeded', function() {
                debugSuccess('✅ Successfully subscribed to channel', channelName);
                
                // Debug event binding
                debugInfo('Setting up event listeners on channel');
                
                // Listen for new messages
                channel.bind('message.new', handleNewMessage);
                
                // Listen for typing indicators
                channel.bind('user.typing', handleUserTyping);
                channel.bind('user.stop_typing', handleUserStopTyping);
            });
            
            // Handle subscription error
            channel.bind('pusher:subscription_error', function(error) {
                debugError('❌ Failed to subscribe to channel', { channel: channelName, error });
            });
        }
        
        // Handle incoming messages
        function handleNewMessage(data) {
            debugSuccess('📨 Received new message', data);
            
            // Validate message data
            if (!data || !data.id || !data.content) {
                debugError('❌ Invalid message data received', data);
                return;
            }
            
            // Check for duplicate messages
            if (document.querySelector(`[data-message-id="${data.id}"]`)) {
                debugInfo('📝 Message already exists in DOM, skipping', data.id);
                return;
            }
            
            // Add message to DOM
            appendMessage(data);
            
            // Determine if we should auto-scroll
            setTimeout(function() {
                const isNearBottom = messagesContainer.scrollHeight - messagesContainer.scrollTop - messagesContainer.clientHeight < 150;
                
                if (isNearBottom) {
                    debugInfo('User is near bottom, auto-scrolling');
                    scrollToBottom();
                } else {
                    // Update unread counter
                    unreadMessagesCount++;
                    
                    // Show new message indicator
                    newMessagesIndicator.textContent = `${unreadMessagesCount} tin nhắn mới`;
                    newMessagesIndicator.style.display = 'block';
                    
                    // Add animation
                    newMessagesIndicator.classList.add('animate__animated', 'animate__pulse');
                    setTimeout(() => {
                        newMessagesIndicator.classList.remove('animate__animated', 'animate__pulse');
                    }, 1000);
                    
                    debugInfo('User is scrolled up, showing indicator', { unreadCount: unreadMessagesCount });
                }
            }, 100);
        }
        
        // Handle typing indicator
        function handleUserTyping(data) {
            debugInfo('User typing', data);
            if (data.user_id !== currentUserId) {
                showTypingIndicator(data.user_name);
            }
        }
        
        // Handle stop typing
        function handleUserStopTyping(data) {
            debugInfo('User stopped typing', data);
            if (data.user_id !== currentUserId) {
                hideTypingIndicator();
            }
        }
        
        // Send message handler
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const content = messageContent.value.trim();
            if (!content) {
                return;
            }
            
            // Disable submit button
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
            }
            
            // Send the message
            debugInfo('Sending message', { content: content.substring(0, 50) + (content.length > 50 ? '...' : '') });
            
            const formData = new FormData(this);
            const url = this.getAttribute('action');
            
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Network response error: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // Clear input
                messageContent.value = '';
                messageContent.style.height = '38px';
                
                // Debug response
                debugSuccess('Server response', data);
                
                // Add message to DOM
                if (!document.querySelector(`[data-message-id="${data.message.id}"]`)) {
                    const messageData = {
                        id: data.message.id,
                        content: data.message.content,
                        sender_id: data.sender.id,
                        sender_name: data.sender.name,
                        sent_at: data.message.sent_at
                    };
                    
                    debugInfo('Adding own message to DOM', messageData);
                    appendMessage(messageData, true);
                    
                    // Scroll to bottom
                    scrollToBottom();
                }
            })
            .catch(error => {
                debugError('Error sending message', error);
                alert('Có lỗi xảy ra khi gửi tin nhắn. Vui lòng thử lại!');
            })
            .finally(() => {
                // Re-enable submit button
                if (submitButton) {
                    submitButton.disabled = false;
                }
            });
        });
        
        // Auto-resize textarea
        messageContent.addEventListener('input', function() {
            this.style.height = '38px';
            this.style.height = (this.scrollHeight) + 'px';
            
            // Handle typing indicators
            handleTypingEvent();
        });
        
        // Typing indicator logic
        let typingTimer;
        function handleTypingEvent() {
            clearTimeout(typingTimer);
            
            // Only send typing event if connected
            if (pusher && pusher.connection.state === 'connected') {
                sendTypingEvent(true);
                
                // Set timeout to stop typing
                typingTimer = setTimeout(function() {
                    sendTypingEvent(false);
                }, 3000);
            }
        }
        
        // Send typing status to server
        function sendTypingEvent(isTyping) {
            const endpoint = isTyping ? 
                `/chat-groups/${chatGroupId}/typing` : 
                `/chat-groups/${chatGroupId}/stop-typing`;
                
            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            }).catch(error => {
                debugError(`Error sending ${isTyping ? 'typing' : 'stop typing'} event`, error);
            });
        }
        
        // Append message to chat
        function appendMessage(message, isMine) {
            debugInfo('Appending message to DOM', message);
            const isMyMessage = isMine || message.sender_id === currentUserId;
            
            const messageHtml = `
                <div class="message-item mb-3 ${isMyMessage ? 'text-end' : ''}" data-message-id="${message.id}">
                    <div class="d-inline-block message-bubble p-2 px-3 rounded-3 ${isMyMessage ? 'bg-primary text-white' : 'bg-light'}" 
                         style="max-width: 75%;">
                        ${!isMyMessage ? `<div class="fw-bold mb-1 small">${message.sender_name || 'Unknown User'}</div>` : ''}
                        <div class="message-content">${message.content}</div>
                        <div class="message-time small ${isMyMessage ? 'text-white-50' : 'text-muted'} mt-1">
                            ${message.sent_at}
                        </div>
                    </div>
                </div>
            `;
            
            // Add to DOM
            messageList.insertAdjacentHTML('beforeend', messageHtml);
            
            // Highlight new message
            const newMessageElement = document.querySelector(`[data-message-id="${message.id}"]`);
            if (newMessageElement) {
                newMessageElement.classList.add('animate__animated', 'animate__fadeIn');
                setTimeout(() => {
                    newMessageElement.classList.remove('animate__animated', 'animate__fadeIn');
                }, 2000);
            }
            
            // Auto-scroll for own messages
            if (isMine) {
                scrollToBottom();
            }
        }
        
        // Typing indicator UI functions
        function showTypingIndicator(userName) {
            const typingIndicator = document.getElementById('typing-indicator');
            const typingName = typingIndicator.querySelector('.typing-name');
            
            if (typingName) {
                typingName.textContent = userName;
            }
            
            typingIndicator.style.display = 'block';
            
            // Auto-hide after timeout
            setTimeout(function() {
                hideTypingIndicator();
            }, 3000);
        }
        
        function hideTypingIndicator() {
            const typingIndicator = document.getElementById('typing-indicator');
            typingIndicator.style.display = 'none';
        }
        
        // Handle scroll events
        messagesContainer.addEventListener('scroll', function() {
            // Save scroll position
            localStorage.setItem('chatScrollPosition-' + chatGroupId, messagesContainer.scrollTop);
            
            // Update UI based on scroll position
            const isNearBottom = messagesContainer.scrollHeight - messagesContainer.scrollTop - messagesContainer.clientHeight < 100;
            
            if (isNearBottom) {
                // Reset unread counter when scrolled to bottom
                unreadMessagesCount = 0;
                newMessagesIndicator.style.display = 'none';
            }
            
            // Toggle scroll button visibility
            scrollButton.style.display = isNearBottom ? 'none' : 'block';
        });
        
        // Restore scroll position or scroll to bottom
        const savedScrollPosition = localStorage.getItem('chatScrollPosition-' + chatGroupId);
        if (savedScrollPosition) {
            messagesContainer.scrollTop = parseInt(savedScrollPosition);
        } else {
            scrollToBottom();
        }
        
        // New message indicator click handler
        newMessagesIndicator.addEventListener('click', function() {
            scrollToBottom();
        });
        
        // Add scroll-to-bottom button
        const scrollButton = document.createElement('button');
        scrollButton.className = 'btn btn-sm btn-primary rounded-circle scroll-bottom-btn';
        scrollButton.innerHTML = '<i class="fas fa-arrow-down"></i>';
        scrollButton.style.display = 'none';
        
        document.querySelector('.card-body').appendChild(scrollButton);
        
        scrollButton.addEventListener('click', function() {
            scrollToBottom();
        });
        
        // Check connection periodically
        setInterval(function() {
            if (pusher && pusher.connection.state !== 'connected') {
                debugInfo('Connection check - current state:', pusher.connection.state);
                pusher.connect();
            }
        }, 5000);
    });
</script>
@endsection