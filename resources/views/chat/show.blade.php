@extends('layouts.app')

@section('title', $chatGroup->name . ' - Chat')

@section('styles')
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
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const messagesContainer = document.getElementById('messages-container');
        const messageList = document.getElementById('message-list');
        const messageForm = document.getElementById('messageForm');
        const messageContent = document.getElementById('messageContent');
        
        // Biến để kiểm tra người dùng có đang cuộn lên để xem tin nhắn cũ không
        let isScrolledUp = false;
        let hasNewMessages = false;
        
        // Auto-resize textarea and typing indicator
        let typingTimer;
        const typingInterval = 2000; // 2 giây
        
        messageContent.addEventListener('input', function() {
            // Auto-resize
            this.style.height = '38px';
            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
            
            // Emit typing event
            clearTimeout(typingTimer);
            
            // Gửi sự kiện typing nếu có nội dung
            if (this.value.trim().length > 0) {
                // Gửi yêu cầu để broadcast sự kiện typing
                fetch('{{ route("chat.typing", $chatGroup->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }).catch(err => console.error('Error sending typing indicator:', err));
            }
            
            // Dừng typing sau khoảng thời gian
            typingTimer = setTimeout(() => {
                // Gửi yêu cầu để dừng broadcast sự kiện typing nếu người dùng dừng gõ
                fetch('{{ route("chat.stop-typing", $chatGroup->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }).catch(err => console.error('Error sending stop typing indicator:', err));
            }, typingInterval);
        });
        
        // Theo dõi vị trí cuộn
        messagesContainer.addEventListener('scroll', function() {
            const atBottom = (messagesContainer.scrollHeight - messagesContainer.scrollTop - messagesContainer.clientHeight) < 50;
            isScrolledUp = !atBottom;
            
            // Nếu người dùng cuộn xuống dưới và có tin nhắn mới, xóa thông báo tin nhắn mới
            if (atBottom && hasNewMessages) {
                hasNewMessages = false;
                // Xóa thông báo nếu có
                const newMessageNotification = document.getElementById('new-message-notification');
                if (newMessageNotification) {
                    newMessageNotification.remove();
                }
            }
        });
        
        // Scroll to bottom of messages
        const scrollToBottom = (force = false) => {
            if (force || !isScrolledUp) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            } else if (!document.getElementById('new-message-notification')) {
                // Nếu người dùng đang cuộn lên và không có thông báo, hiển thị thông báo tin nhắn mới
                hasNewMessages = true;
                const notification = document.createElement('div');
                notification.id = 'new-message-notification';
                notification.className = 'position-absolute bottom-0 start-50 translate-middle-x mb-3 bg-primary text-white px-3 py-2 rounded-pill shadow-sm';
                notification.style.zIndex = '100';
                notification.innerHTML = 'Tin nhắn mới <i class="fas fa-arrow-down ms-1"></i>';
                notification.style.cursor = 'pointer';
                notification.onclick = () => scrollToBottom(true);
                messagesContainer.parentNode.appendChild(notification);
            }
        };
        
        // Gọi scrollToBottom() ngay khi load trang
        scrollToBottom(true);
        
        // Khởi tạo Pusher
        const pusherKey = '{{ config("broadcasting.connections.pusher.key") }}';
        const pusherCluster = '{{ config("broadcasting.connections.pusher.options.cluster") }}';
        
        if (pusherKey) {
            const pusher = new Pusher(pusherKey, {
                cluster: pusherCluster,
                encrypted: true,
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                }
            });
            
            // Đăng ký kênh riêng tư của nhóm chat
            const channel = pusher.subscribe('private-chat-group.{{ $chatGroup->id }}');
            
            // Theo dõi trạng thái kết nối
            pusher.connection.bind('state_change', function(states) {
                console.log('Pusher connection state:', states.current);
            });

            // Theo dõi lỗi
            pusher.connection.bind('error', function(err) {
                console.error('Pusher connection error:', err);
            });

            // Theo dõi thành công
            channel.bind('subscription_succeeded', function() {
                console.log('Successfully subscribed to channel');
            });

            // Theo dõi lỗi kênh
            channel.bind('subscription_error', function(status) {
                console.error('Error subscribing to channel:', status);
            });
            
            // Lắng nghe sự kiện tin nhắn mới
            channel.bind('new-message', function(data) {
                console.log('Received message:', data);
                // Thêm tin nhắn mới vào giao diện
                appendMessage(data, false);
                
                // Cuộn xuống dưới cùng tùy theo trạng thái
                scrollToBottom();
            });
            
            // Lắng nghe sự kiện người dùng đang gõ
            channel.bind('typing', function(data) {
                console.log('User typing:', data);
                const typingIndicator = document.getElementById('typing-indicator');
                const typingName = typingIndicator.querySelector('.typing-name');
                
                // Không hiển thị typing nếu là chính mình
                if (data.user_id == {{ Auth::id() }}) {
                    return;
                }
                
                typingName.textContent = data.user_name;
                typingIndicator.style.display = 'block';
            });
            
            // Lắng nghe sự kiện người dùng dừng gõ
            channel.bind('stop-typing', function(data) {
                console.log('User stopped typing:', data);
                const typingIndicator = document.getElementById('typing-indicator');
                
                // Nếu không có người dùng nào đang gõ nữa
                if (data.user_id != {{ Auth::id() }}) {
                    typingIndicator.style.display = 'none';
                }
            });
        }
        
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
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
                
                // Append message
                appendMessage({
                    id: data.message.id,
                    content: data.message.content,
                    sender_id: data.sender.id,
                    sender_name: data.sender.name,
                    sent_at: data.message.sent_at
                }, true);
                
                // Scroll to bottom
                scrollToBottom();
            })
            .catch(error => {
                console.error('Error sending message:', error);
                alert('Có lỗi xảy ra khi gửi tin nhắn. Vui lòng thử lại!');
            });
        });
        
        // Hàm thêm tin nhắn vào giao diện
        function appendMessage(message, isMine) {
            const currentUserId = {{ Auth::id() }};
            const isMyMessage = isMine || message.sender_id === currentUserId;
            
            const messageHtml = `
                <div class="message-item mb-3 ${isMyMessage ? 'text-end' : ''}">
                    <div class="d-inline-block message-bubble p-2 px-3 rounded-3 ${isMyMessage ? 'bg-primary text-white' : 'bg-light'}" 
                         style="max-width: 75%;">
                        ${!isMyMessage ? `<div class="fw-bold mb-1 small">${message.sender_name}</div>` : ''}
                        <div class="message-content">${message.content}</div>
                        <div class="message-time small ${isMyMessage ? 'text-white-50' : 'text-muted'} mt-1">
                            ${message.sent_at}
                        </div>
                    </div>
                </div>
            `;
            
            messageList.insertAdjacentHTML('beforeend', messageHtml);
        }
    });
</script>
@endsection 