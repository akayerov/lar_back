<?php

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
/*
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
*/
Broadcast::channel('survey.{survey_id}', function ($user, $survey_id) {
    return $user;
});

Broadcast::channel('room.{room_id}', function ($user, $room_id) {
    //dd($user);
    //  return (int) $user->rooms->contains($room_id);
    return $user;
});

Broadcast::channel('everywhere', function ($user) {
    return $user;
});

Broadcast::channel('chat', function ($user) {
    return true;
});
