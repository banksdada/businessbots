<?php

namespace App\Providers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerRelaxedSmtpTransport();
    }

    /**
     * Registers a custom "smtp-relaxed" mail transport, used only when
     * config('mail.mailers.smtp.transport') is set to it (see config/mail.php,
     * controlled by the MAIL_VERIFY_PEER env var).
     *
     * Why this exists: Laravel's built-in "smtp" transport creator
     * (MailManager::configureSmtpTransport) only ever applies "source_ip" and
     * "timeout" from the mailer config — it silently ignores any "stream" key,
     * so there is no supported way to relax TLS certificate-name verification
     * for a single SMTP host through config alone. webmail.enmail.co's
     * certificate is issued to a different name (relay.plus.net — it's a
     * rebrand/reseller in front of Plusnet's mail infrastructure), so the
     * default strict check rejects the connection outright even though the
     * credentials and encryption are both fine.
     *
     * This builds the exact same Symfony EsmtpTransport Laravel's own "smtp"
     * driver would, then explicitly sets stream options on its SocketStream
     * to skip only the certificate-name check — never disabling encryption.
     */
    protected function registerRelaxedSmtpTransport(): void
    {
        Mail::extend('smtp-relaxed', function (array $config) {
            $factory = new EsmtpTransportFactory;

            $scheme = $config['scheme'] ?? (($config['port'] ?? null) == 465 ? 'smtps' : 'smtp');

            $transport = $factory->create(new Dsn(
                $scheme,
                $config['host'],
                $config['username'] ?? null,
                $config['password'] ?? null,
                $config['port'] ?? null,
                $config,
            ));

            $stream = $transport->getStream();

            if ($stream instanceof SocketStream) {
                $stream->setStreamOptions(array_merge($stream->getStreamOptions(), [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ],
                ]));
            }

            return $transport;
        });
    }
}
