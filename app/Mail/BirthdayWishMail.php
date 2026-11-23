<?php

namespace App\Mail;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BirthdayWishMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Employee $employee
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Happy Birthday, '.$this->employee->name.'! 🎂'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.birthday-wish'
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
