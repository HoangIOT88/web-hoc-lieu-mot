<?php

use App\Models\ChatGroup;
use Illuminate\Support\Facades\Broadcast;

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

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Private channel cho chat group
Broadcast::channel('chat-group.{groupId}', function ($user, $groupId) {
    // Kiểm tra xem người dùng có phải là thành viên của nhóm chat hay không
    return \DB::table('chat_group_members')
        ->where('group_id', $groupId)
        ->where('user_id', $user->id)
        ->exists();
});
