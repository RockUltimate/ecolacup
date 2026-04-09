<?php

namespace App\Mail;

use App\Models\Prihlaska;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PrihlaskaPotvrzeniMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Prihlaska $prihlaska,
        public readonly string $mode = 'created',
    )
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mode === 'updated'
                ? 'Aktualizace přihlášky #'.$this->prihlaska->id
                : 'Potvrzení přihlášky #'.$this->prihlaska->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.prihlaska-potvrzeni',
            with: [
                'mode' => $this->mode,
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $pdfBytes = Pdf::loadView('prihlasky.pdf', [
            'prihlaska' => $this->prihlaska->load([
                'udalost.moznosti',
                'osoba',
                'kun',
                'kunTandem',
                'polozky.moznost',
                'ustajeniChoices.ustajeni',
            ]),
        ])->output();

        return [
            Attachment::fromData(
                fn () => $pdfBytes,
                'prihlaska_'.$this->prihlaska->id.'.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
