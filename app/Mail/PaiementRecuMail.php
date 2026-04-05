<?php

namespace App\Mail;

use App\Models\depot;
use App\Models\paiement;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use Illuminate\Contracts\Queue\ShouldQueue;

class PaiementRecuMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $depot;
    public $montant;
    public $mode_paiement;

    /**
     * Create a new message instance.
     */
    public function __construct(depot $depot, $montant, $mode_paiement = 'Espèces')
    {
        $this->depot = $depot;
        $this->montant = $montant;
        $this->mode_paiement = $mode_paiement;
    }

    /**
     * Get the message envelope.
     */
    public function Envelope(): Envelope
    {
        return new Envelope(
            subject: '🧾 Reçu de Paiement - WashPro',
        );
    }

    /**
     * Get the message content definition.
     */
    public function Content(): Content
    {
        return new Content(
            view: 'emails.paiement-recu',
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
