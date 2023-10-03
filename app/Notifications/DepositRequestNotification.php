<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DepositRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $payment;

    public function __construct($payment) {
        $this->payment = $payment;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $token = \Crypt::encryptString('deposit2023|' . $this->payment->payment_id);
        return (new MailMessage)
            ->subject('Deposit Approval')
            ->greeting('Deposit Approval')
            ->line('Transaction ID: ' . $this->payment->payment_id)
            ->line('Deposit Amount: ' . $this->payment->amount)
            ->line('Click the button to proceed with approval')
            ->action('Approval', route('approval', [
                'token' => $token,
            ]))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
