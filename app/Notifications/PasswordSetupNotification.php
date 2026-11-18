<?php

namespace App\Notifications;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordSetupNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $token,
        public ?Employee $employee = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject(
                $this->employee
                    ? 'Welcome to the Team | Employee Management System'
                    : 'Set Up Your Password | Employee Management System'
            )
            ->view('emails.password-setup', [
                'user' => $notifiable,
                'url' => $url,
                'employee' => $this->employee,
            ]);
    }
}
