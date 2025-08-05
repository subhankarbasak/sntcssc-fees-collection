<?php

// app/Listeners/SendUserCreatedNotification.php
namespace App\Listeners;

use App\Events\UserCreated;
use Illuminate\Support\Facades\Notification;
use App\Notifications\UserCreatedNotification;

class SendUserCreatedNotification
{
    public function handle(UserCreated $event)
    {
        Notification::send($event->user, new UserCreatedNotification());
    }
}