<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $code,
        public readonly string $purposeLabel,
        public readonly int $ttlMinutes = 5,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "{$this->code} adalah Kode Verifikasi {$this->purposeLabel} - MyDream",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp-code',
            text: 'emails.otp-code-text',
        );
    }
}
