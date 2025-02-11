<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('chat-channel.{userId}', function (User $user, $userId) {
    // Ensure that the authenticated user matches the userId in the channel
    // dd($user);
    return (int) $user->id === (int) $userId;
});
Broadcast::channel('chat-channel.{userId}', function (User $user, $userId) {
    // Ensure that the authenticated user matches the userId in the channel
    // dd($user);
    return (int) $user->id === (int) $userId;
});
