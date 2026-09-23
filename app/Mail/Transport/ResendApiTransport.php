<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * Sends mail through Resend's HTTP API directly:
 * https://resend.com/docs/api-reference/emails/send-email
 *
 * Deliberately does NOT use the resend/resend-php SDK or Laravel's built-in
 * ResendTransport (which requires that package) — this app's SMTP mail via
 * webmail.enmail.co turned out to be blocked at the provider level for
 * external app access (correct credentials, works for webmail/IMAP, rejected
 * for SMTP AUTH), so Resend was chosen as a proper transactional email
 * service instead. Talking to its API directly with Laravel's own HTTP
 * client avoids adding a new Composer dependency altogether.
 *
 * Registered as the "resend-http" transport in AppServiceProvider, used by
 * the "resend" mailer in config/mail.php when MAIL_MAILER=resend. Requires
 * RESEND_API_KEY (see .env.example) — get one free at https://resend.com.
 */
class ResendApiTransport extends AbstractTransport
{
    public function __construct(protected string $apiKey)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = $message->getOriginalMessage();

        if (! $email instanceof Email) {
            throw new TransportException('The Resend API transport only supports Symfony\Component\Mime\Email messages.');
        }

        $from = $email->getFrom();

        if (empty($from)) {
            throw new TransportException('Cannot send via Resend: the message has no "from" address.');
        }

        $replyTo = $email->getReplyTo();

        $payload = array_filter([
            'from' => $this->formatAddress($from[0]),
            'to' => array_map($this->formatAddress(...), $email->getTo()),
            'cc' => array_map($this->formatAddress(...), $email->getCc()),
            'bcc' => array_map($this->formatAddress(...), $email->getBcc()),
            'reply_to' => $replyTo ? $this->formatAddress($replyTo[0]) : null,
            'subject' => $email->getSubject() ?? '',
            'html' => $email->getHtmlBody(),
            'text' => $email->getTextBody(),
        ], fn ($value) => $value !== null && $value !== []);

        $response = Http::withToken($this->apiKey)
            ->acceptJson()
            ->post('https://api.resend.com/emails', $payload);

        if ($response->failed()) {
            throw new TransportException(
                "Resend API request failed with status {$response->status()}: {$response->body()}"
            );
        }
    }

    protected function formatAddress(Address $address): string
    {
        return $address->getName() !== ''
            ? sprintf('%s <%s>', $address->getName(), $address->getAddress())
            : $address->getAddress();
    }

    public function __toString(): string
    {
        return 'resend+api://api.resend.com';
    }
}
