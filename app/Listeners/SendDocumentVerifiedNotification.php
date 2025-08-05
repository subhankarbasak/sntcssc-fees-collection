<?php

// app/Listeners/SendDocumentVerifiedNotification.php

namespace App\Listeners;

use App\Events\DocumentVerified;
use App\Notifications\DocumentVerifiedNotification;
use Illuminate\Support\Facades\Notification;

class SendDocumentVerifiedNotification
{
    public function handle(DocumentVerified $event)
    {
        // Notify the document owner about verification
        $document = $event->document;
        $owner = $document->documentable->user;
        
        Notification::send($owner, new DocumentVerifiedNotification($document));
    }
}