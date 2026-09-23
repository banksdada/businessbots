<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file stores configuration for third-party services used by
    | BusinessBots. The actual secret values should stay in your .env
    | file / Coolify Environment Variables.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Postmark
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Amazon SES
    |--------------------------------------------------------------------------
    */

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Resend
    |--------------------------------------------------------------------------
    */

    'resend' => [
        // Laravel's built-in Resend mail transport (see MailManager::
        // createResendTransport()) specifically reads services.resend.key —
        // not api_key — so this must be named exactly this for MAIL_MAILER=
        // resend to actually pick up the API key.
        'key' => env('RESEND_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Slack
    |--------------------------------------------------------------------------
    */

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Meta - WhatsApp / Instagram
    |--------------------------------------------------------------------------
    |
    | These values are used for:
    |
    | - WhatsApp Cloud API
    | - Meta webhook verification
    | - WhatsApp message sending
    | - Instagram integration later
    |
    */

    'meta' => [
        'app_id' => env('META_APP_ID'),

        'app_secret' => env('META_APP_SECRET'),

        'whatsapp_token' => env('META_WHATSAPP_TOKEN'),

        'whatsapp_phone_number_id' => env(
            'META_WHATSAPP_PHONE_NUMBER_ID'
        ),

        'webhook_verify_token' => env(
            'META_WEBHOOK_VERIFY_TOKEN'
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | PyRunner
    |--------------------------------------------------------------------------
    |
    | BusinessBots uses PyRunner to execute Python automation scripts.
    |
    */

    'pyrunner' => [
        'worker_token' => env(
            'PYRUNNER_WORKER_TOKEN'
        ),

        'api_token' => env(
            'PYRUNNER_API_TOKEN'
        ),

        'intent_webhook' => env(
            'PYRUNNER_INTENT_WEBHOOK_URL'
        ),

        'reply_webhook' => env(
            'PYRUNNER_REPLY_WEBHOOK_URL'
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | LinkedIn
    |--------------------------------------------------------------------------
    */

    'linkedin' => [
        'client_id' => env(
            'LINKEDIN_CLIENT_ID'
        ),

        'client_secret' => env(
            'LINKEDIN_CLIENT_SECRET'
        ),

        'redirect_uri' => env(
            'LINKEDIN_REDIRECT_URI'
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google
    |--------------------------------------------------------------------------
    */

    'google' => [
        'client_id' => env(
            'GOOGLE_CLIENT_ID'
        ),

        'client_secret' => env(
            'GOOGLE_CLIENT_SECRET'
        ),

        'redirect_uri' => env(
            'GOOGLE_REDIRECT_URI'
        ),
    ],

];
