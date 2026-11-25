<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ArtistRegistrationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $registrationId,
        public readonly string $applicantName,
        public readonly string $registrationTypeLabel,
        public readonly string $submissionDate,
        public readonly string $statusLabel,
        public readonly string $countryName,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Opportunity Hub - Registration Received ('.$this->registrationId.')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-confirmation',
        );
    }
}
