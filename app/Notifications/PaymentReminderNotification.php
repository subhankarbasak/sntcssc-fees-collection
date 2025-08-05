<?php

// app/Notifications/PaymentReminderNotification.php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    public $student;
    public $fees;
    
    public function __construct($student, $fees)
    {
        $this->student = $student;
        $this->fees = $fees;
    }
    
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }
    
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Payment Reminder')
            ->greeting('Hello ' . $this->student->user->name)
            ->line('This is a reminder that you have pending fees to be paid.')
            ->line('Total Due Amount: ₹' . number_format($this->fees->sum('amount'), 2))
            ->action('Pay Now', url('/student/dashboard'))
            ->line('Thank you for using our application!');
    }
    
    public function toDatabase($notifiable)
    {
        return [
            'student_id' => $this->student->id,
            'message' => 'You have pending fees to be paid.',
            'amount' => $this->fees->sum('amount'),
        ];
    }
}