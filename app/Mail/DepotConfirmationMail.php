<?php

namespace App\Mail;

use App\Models\depot;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use Illuminate\Contracts\Queue\ShouldQueue;

class DepotConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $depot;

    /**
     * Create a new message instance.
     */
    public function __construct(Depot $depot)
    {
        $this->depot = $depot;
    }

    /**
     * Get the message envelope.
     */
    public function Envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmation de votre dépôt - WashPro',
        );
    }

    /**
     * Get the message content definition.
     */
    public function Content(): Content
    {
        return new Content(
            view: 'emails.depot-confirmation',
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
