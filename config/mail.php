<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    */

    'default' => env('MAIL_MAILER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | This app was originally running on Laravel's built-in mail defaults
    | (no config/mail.php existed). This file was added specifically to fix
    | a TLS certificate mismatch when sending via webmail.enmail.co: that
    | host's certificate is actually issued to "relay.plus.net" (it's a
    | reseller/rebrand sitting in front of Plusnet's mail infrastructure),
    | so PHP's default strict certificate-name check rejects the connection.
    |
    | Laravel's built-in "smtp" transport has no supported way to relax just
    | the certificate-name check via config (its transport factory ignores a
    | "stream" key entirely) — so when MAIL_VERIFY_PEER=false, this switches
    | to a custom "smtp-relaxed" transport registered in AppServiceProvider
    | that builds the same underlying connection but skips that one check.
    | Leave MAIL_VERIFY_PEER unset (or true) everywhere else to keep full
    | verification, including if this provider ever fixes their certificate.
    |
    */

    'mailers' => [

        'smtp' => [
            'transport' => env('MAIL_VERIFY_PEER', true) ? 'smtp' : 'smtp-relaxed',
            'scheme' => env('MAIL_SCHEME'),
            'url' => env('MAIL_URL'),
            'host' => env('MAIL_HOST', '127.0.0.1'),
            'port' => env('MAIL_PORT', 2525),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'postmark' => [
            'transport' => 'postmark',
        ],

        'resend' => [
            // 'resend-http' (see AppServiceProvider + app/Mail/Transport/
            // ResendApiTransport.php) talks to Resend's API directly with
            // Laravel's own HTTP client — not Laravel's built-in "resend"
            // transport, which needs the resend/resend-php Composer package
            // this app doesn't otherwise need.
            'transport' => 'resend-http',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
        ],

        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => [
                'ses',
                'postmark',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],

];
