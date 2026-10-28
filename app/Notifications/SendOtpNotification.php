<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendOtpNotification extends Notification
{
    use Queueable;

    public function __construct(public string $otp) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $name = $notifiable->name ?? 'User';

        return (new MailMessage)
            ->subject('Your Judiciary Portal Login Verification Code')
            ->greeting("Hello {$name},")
            ->line('Use the following One-Time Password (OTP) to securely log in to the Judiciary Internship Management Portal:')
            ->line("# **{$this->otp}**")
            ->line('This verification code will expire in **10 minutes**. For your security, never share this code with anyone.')
            ->line('If you did not request this login code, please secure your account immediately.');
    }
}
