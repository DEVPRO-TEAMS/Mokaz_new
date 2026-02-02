<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class reservatierNotifier extends Mailable
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

    /**
     * Get the message envelope.
    */
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
            view: 'mail.customerNotify',
            with: [
                'messages' => $this->emailData['messages'] ?? '',
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
