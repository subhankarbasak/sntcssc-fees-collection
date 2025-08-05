<?php

// app/Notifications/DocumentVerifiedNotification.php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentVerifiedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    public $document;
    
    public function __construct($document)
    {
        $this->document = $document;
    }
    
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }
    
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Document Verified')
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your document "' . $this->document->title . '" has been verified.')
            ->line('Verification Date: ' . now()->format('d M Y'))
            ->action('View Document', url('/student/documents'))
            ->line('Thank you for using our application!');
    }
    
    public function toDatabase($notifiable)
    {
        return [
            'document_id' => $this->document->id,
            'message' => 'Your document has been verified.',
            'title' => $this->document->title,
        ];
    }
}