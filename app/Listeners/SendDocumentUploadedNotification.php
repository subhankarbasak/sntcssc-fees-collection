<?php

// app/Listeners/SendDocumentUploadedNotification.php
namespace App\Listeners;

use App\Events\DocumentUploaded;
use App\Notifications\DocumentUploadedNotification;
use Illuminate\Support\Facades\Notification;

class SendDocumentUploadedNotification
{
    public function handle(DocumentUploaded $event)
    {
        // Notify admins about the new document
        $admins = \App\Models\User::role('admin')->get();
        Notification::send($admins, new DocumentUploadedNotification($event->document));
    }
}