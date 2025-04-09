<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pusher Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        pre {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            max-height: 400px;
            overflow-y: auto;
        }
        .card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <h1>Pusher Authentication Test</h1>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Connection Status
                    </div>
                    <div class="card-body">
                        <div id="connection-status" class="alert alert-info">Connecting...</div>
                        <div class="mb-3">
                            <label for="channel-name" class="form-label">Channel Name</label>
                            <input type="text" class="form-control" id="channel-name" value="private-chat-group.1">
                        </div>
                        <button id="subscribe-btn" class="btn btn-primary">Subscribe</button>
                        <button id="unsubscribe-btn" class="btn btn-danger">Unsubscribe</button>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        Subscription Status
                    </div>
                    <div class="card-body">
                        <div id="subscription-status" class="alert alert-secondary">Not subscribed</div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        User Info
                    </div>
                    <div class="card-body">
                        <div><strong>User ID:</strong> {{ Auth::id() }}</div>
                        <div><strong>User Name:</strong> {{ Auth::user()->name }}</div>
                        <div><strong>User Role:</strong> {{ Auth::user()->role }}</div>
                        <div class="mt-2">
                            <strong>CSRF Token:</strong>
                            <span id="csrf-token">{{ csrf_token() }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Log</div>
                    <div class="card-body">
                        <pre id="log-output"></pre>
                        <button id="clear-log" class="btn btn-sm btn-secondary">Clear Log</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const logOutput = document.getElementById('log-output');
            const connectionStatus = document.getElementById('connection-status');
            const subscriptionStatus = document.getElementById('subscription-status');
            const channelNameInput = document.getElementById('channel-name');
            const subscribeBtn = document.getElementById('subscribe-btn');
            const unsubscribeBtn = document.getElementById('unsubscribe-btn');
            const clearLogBtn = document.getElementById('clear-log');
            
            let currentChannel = null;
            let pusher = null;
            
            // Helper to log messages
            function log(message, data = null) {
                const timestamp = new Date().toISOString().substr(11, 8);
                let logMessage = `[${timestamp}] ${message}`;
                
                if (data) {
                    logMessage += '\n' + JSON.stringify(data, null, 2);
                }
                
                logOutput.innerHTML += logMessage + '\n\n';
                logOutput.scrollTop = logOutput.scrollHeight;
            }
            
            // Initialize Pusher
            function initPusher() {
                log('Initializing Pusher');
                
                const key = '{{ config("broadcasting.connections.pusher.key") }}';
                const cluster = '{{ config("broadcasting.connections.pusher.options.cluster") }}';
                
                pusher = new Pusher(key, {
                    cluster: cluster,
                    authEndpoint: '/broadcasting/auth',
                    auth: {
                        headers: {
                            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    }
                });
                
                log('Pusher configuration', {
                    key: key,
                    cluster: cluster,
                    csrf_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                });
                
                // Connection events
                pusher.connection.bind('connecting', () => {
                    log('Pusher connecting');
                    connectionStatus.className = 'alert alert-info';
                    connectionStatus.textContent = 'Connecting...';
                });
                
                pusher.connection.bind('connected', () => {
                    log('Pusher connected successfully');
                    connectionStatus.className = 'alert alert-success';
                    connectionStatus.textContent = 'Connected';
                    
                    log('Socket ID: ' + pusher.connection.socket_id);
                });
                
                pusher.connection.bind('failed', () => {
                    log('Pusher connection failed');
                    connectionStatus.className = 'alert alert-danger';
                    connectionStatus.textContent = 'Connection Failed';
                });
                
                pusher.connection.bind('disconnected', () => {
                    log('Pusher disconnected');
                    connectionStatus.className = 'alert alert-warning';
                    connectionStatus.textContent = 'Disconnected';
                });
                
                pusher.connection.bind('error', (err) => {
                    log('Pusher connection error', err);
                });
                
                pusher.connection.bind('message', (msg) => {
                    log('Pusher message', msg);
                });
            }
            
            // Subscribe to channel
            function subscribeToChannel() {
                const channelName = channelNameInput.value;
                
                if (!channelName) {
                    alert('Please enter a channel name');
                    return;
                }
                
                if (currentChannel) {
                    log('Unsubscribing from current channel first');
                    pusher.unsubscribe(currentChannel.name);
                    currentChannel = null;
                }
                
                log(`Subscribing to channel: ${channelName}`);
                
                try {
                    currentChannel = pusher.subscribe(channelName);
                    
                    currentChannel.bind('subscription_succeeded', () => {
                        log(`Successfully subscribed to ${channelName}`);
                        subscriptionStatus.className = 'alert alert-success';
                        subscriptionStatus.textContent = `Subscribed to ${channelName}`;
                    });
                    
                    currentChannel.bind('subscription_error', (error) => {
                        log(`Error subscribing to ${channelName}`, error);
                        subscriptionStatus.className = 'alert alert-danger';
                        subscriptionStatus.textContent = `Subscription error: ${error}`;
                    });
                    
                    currentChannel.bind('new-message', (data) => {
                        log(`Received new-message event on ${channelName}`, data);
                    });
                    
                    currentChannel.bind('typing', (data) => {
                        log(`Received typing event on ${channelName}`, data);
                    });
                    
                    currentChannel.bind('stop-typing', (data) => {
                        log(`Received stop-typing event on ${channelName}`, data);
                    });
                } catch (error) {
                    log('Error subscribing to channel', error);
                }
            }
            
            // Unsubscribe from channel
            function unsubscribeFromChannel() {
                if (!currentChannel) {
                    log('No channel to unsubscribe from');
                    return;
                }
                
                const channelName = currentChannel.name;
                log(`Unsubscribing from channel: ${channelName}`);
                
                pusher.unsubscribe(channelName);
                currentChannel = null;
                
                subscriptionStatus.className = 'alert alert-secondary';
                subscriptionStatus.textContent = 'Not subscribed';
            }
            
            // UI Events
            subscribeBtn.addEventListener('click', subscribeToChannel);
            unsubscribeBtn.addEventListener('click', unsubscribeFromChannel);
            
            clearLogBtn.addEventListener('click', () => {
                logOutput.innerHTML = '';
            });
            
            // Initialize
            initPusher();
            
            // Auto-subscribe to default channel after 1 second
            setTimeout(() => {
                if (pusher.connection.state === 'connected') {
                    subscribeToChannel();
                }
            }, 1000);
        });
    </script>
</body>
</html> 