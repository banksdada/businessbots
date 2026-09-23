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
    | The "stream" block below is the standard Laravel/Symfony Mailer way to
    | relax that specific check while keeping the connection encrypted. It
    | only takes effect when MAIL_VERIFY_PEER is explicitly set to false in
    | .env / Coolify — every other deployment of this app (or this one, if
    | the provider ever fixes their certificate) keeps full verification by
    | default.
    |
    */

    'mailers' => [

        'smtp' => [
            'transport' => 'smtp',
            'scheme' => env('MAIL_SCHEME'),
            'url' => env('MAIL_URL'),
            'host' => env('MAIL_HOST', '127.0.0.1'),
            'port' => env('MAIL_PORT', 2525),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
            'stream' => [
                'ssl' => [
                    // Set MAIL_VERIFY_PEER=false in .env only for a host whose
                    // certificate is known to be issued to a different name
                    // (like webmail.enmail.co / relay.plus.net here) but that
                    // you otherwise trust. Leave unset/true everywhere else.
                    'verify_peer' => env('MAIL_VERIFY_PEER', true),
                    'verify_peer_name' => env('MAIL_VERIFY_PEER', true),
                    'allow_self_signed' => ! env('MAIL_VERIFY_PEER', true),
                ],
            ],
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'postmark' => [
            'transport' => 'postmark',
        ],

        'resend' => [
            'transport' => 'resend',
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
