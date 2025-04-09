<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use App\Models\ChatGroupMember;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatGroup;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Debug channel - always return true for testing
Broadcast::channel('debug-channel', function ($user) {
    Log::debug('Debug channel authorization attempt', [
        'user_id' => $user->id,
        'email' => $user->email,
        'timestamp' => now()->toDateTimeString()
    ]);
    
    return true;
});

// For debugging: Private channel for each user
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    $authorized = (int) $user->id === (int) $id;
    
    Log::debug('User channel authorization', [
        'user_id' => $user->id,
        'requested_id' => $id,
        'authorized' => $authorized,
        'timestamp' => now()->toDateTimeString()
    ]);
    
    // For debugging, always return true
    return true;
});

// Private chat group channel
Broadcast::channel('private-chat-group.{groupId}', function ($user, $groupId) {
    // Log detailed information
    $isAdmin = $user->isAdmin();
    $isContentUser = $user->isContentUser();
    
    // Check if user is member of the group
    $isMember = ChatGroupMember::where('user_id', $user->id)
                    ->where('group_id', $groupId)
                    ->exists();
    
    Log::debug('Private chat group channel authorization', [
        'channel' => 'private-chat-group.'.$groupId,
        'user_id' => $user->id,
        'email' => $user->email,
        'group_id' => $groupId,
        'is_admin' => $isAdmin,
        'is_content_user' => $isContentUser,
        'is_member' => $isMember,
        'socket_id' => request()->header('X-Socket-ID', 'not-provided'),
        'timestamp' => now()->toDateTimeString()
    ]);
    
    // For debugging, always return true
    return true;
});

// Regular chat group channel (alternative format)
Broadcast::channel('chat-group.{groupId}', function ($user, $groupId) {
    // Log detailed information
    $isAdmin = $user->isAdmin();
    $isContentUser = $user->isContentUser();
    
    // Check if user is member of the group
    $isMember = ChatGroupMember::where('user_id', $user->id)
                    ->where('group_id', $groupId)
                    ->exists();
    
    Log::debug('Regular chat group channel authorization', [
        'channel' => 'chat-group.'.$groupId,
        'user_id' => $user->id,
        'email' => $user->email,
        'group_id' => $groupId,
        'is_admin' => $isAdmin,
        'is_content_user' => $isContentUser,
        'is_member' => $isMember,
        'socket_id' => request()->header('X-Socket-ID', 'not-provided'),
        'timestamp' => now()->toDateTimeString()
    ]);
    
    // For debugging, always return true
    return true;
});
