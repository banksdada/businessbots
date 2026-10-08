<?php

namespace App\Mail;

use App\Models\ProblemRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DraftReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ProblemRequest $request,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Draft ready to review: {$this->request->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.draft-ready',
        );
    }
}
