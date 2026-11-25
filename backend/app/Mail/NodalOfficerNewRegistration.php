<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NodalOfficerNewRegistration extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $officerName,
        public readonly string $registrationId,
        public readonly string $applicantName,
        public readonly string $registrationTypeLabel,
        public readonly string $countryName,
        public readonly string $applicantEmail,
        public readonly ?string $applicantPhone,
        public readonly string $submissionDate,
        public readonly string $reviewUrl,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Opportunity Hub - New '.$this->registrationTypeLabel.' Registration ('.$this->registrationId.')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.nodal-new-registration',
        );
    }
}
