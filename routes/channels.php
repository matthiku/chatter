<?php

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

Broadcast::channel(
    'App.User.{id}', function ($user, $id) {
        return (int) $user->id === (int) $id;
    }
);

// generic channel for user presence reports (anyone can join)
Broadcast::channel(
    env('MAIN_CHATROOM_NAME', 'chatroom'), function ($user) {
        return $user;
    }
);

// specific channels for specific chat rooms (only chat room members can join)
Broadcast::channel(
    env('MAIN_CHATROOM_NAME', 'chatroom') . '.chatroom.{room}',
    function ($user, App\Room $room) {
        // check if user is member of this room
        return $room->users->contains('id', $user->id);
    }
);
