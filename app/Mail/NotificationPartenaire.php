<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationPartenaire extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
   
    public $emailData;
    public $emailSubject;

    public function __construct($emailData, $emailSubject)
    {
        $this->emailData = $emailData;
        $this->emailSubject = $emailSubject;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.userNotify',
            with: [
                'messageText' => $this->emailData['messages'] ?? '',
                'url' => $this->emailData['url'] ?? '',
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
