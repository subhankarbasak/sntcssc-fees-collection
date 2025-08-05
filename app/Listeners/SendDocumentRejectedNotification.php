<?php

// app/Listeners/SendDocumentRejectedNotification.php
namespace App\Listeners;

use App\Events\DocumentRejected;
use App\Notifications\DocumentRejectedNotification;
use Illuminate\Support\Facades\Notification;

class SendDocumentRejectedNotification
{
    public function handle(DocumentRejected $event)
    {
        // Notify the document owner about rejection
        $document = $event->document;
        $owner = $document->documentable->user;
        
        Notification::send($owner, new DocumentRejectedNotification($document));
    }
}