<?php


// app/Notifications/DocumentUploadedNotification.php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentUploadedNotification extends Notification implements ShouldQueue
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
            ->subject('New Document Uploaded')
            ->greeting('Hello ' . $notifiable->name)
            ->line('A new document has been uploaded by ' . $this->document->documentable->user->name)
            ->line('Document Title: ' . $this->document->title)
            ->line('Document Type: ' . $this->document->type)
            ->action('View Document', url('/admin/documents'))
            ->line('Thank you for using our application!');
    }
    
    public function toDatabase($notifiable)
    {
        return [
            'document_id' => $this->document->id,
            'message' => 'A new document has been uploaded by ' . $this->document->documentable->user->name,
            'title' => $this->document->title,
        ];
    }
}