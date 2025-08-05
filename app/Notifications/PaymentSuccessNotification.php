<?php

// app/Notifications/PaymentSuccessNotification.php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSuccessNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    public $transaction;
    
    public function __construct($transaction)
    {
        $this->transaction = $transaction;
    }
    
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }
    
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Payment Successful')
            ->greeting('Hello ' . $this->transaction->student->user->name)
            ->line('Your payment of ₹' . number_format($this->transaction->amount, 2) . ' has been successfully processed.')
            ->line('Payment Method: ' . ucfirst($this->transaction->payment_method))
            ->line('Transaction ID: ' . $this->transaction->id)
            ->action('View Payment History', url('/student/dashboard'))
            ->line('Thank you for using our application!');
    }
    
    public function toDatabase($notifiable)
    {
        return [
            'transaction_id' => $this->transaction->id,
            'message' => 'Your payment has been successfully processed.',
            'amount' => $this->transaction->amount,
        ];
    }
}