<?php

// config/channels.php
// Per-platform OAuth wiring for the "Connect your channels" onboarding step.
// Adding a new platform is a new entry here + a matching case in
// ChannelOAuthController's token-exchange response mapping — not a new controller.

return [

    'instagram' => [
        'label' => 'Instagram',
        'authorize_url' => 'https://www.facebook.com/v19.0/dialog/oauth',
        'token_url' => 'https://graph.facebook.com/v19.0/oauth/access_token',
        'scope' => 'instagram_basic,instagram_content_publish,pages_show_list,business_management',
        'client_id' => env('META_APP_ID'),
        'client_secret' => env('META_APP_SECRET'),
        'redirect_uri' => env('META_INSTAGRAM_REDIRECT_URI'),
    ],

    'whatsapp' => [
        'label' => 'WhatsApp Business',
        'authorize_url' => 'https://www.facebook.com/v19.0/dialog/oauth',
        'token_url' => 'https://graph.facebook.com/v19.0/oauth/access_token',
        'scope' => 'whatsapp_business_management,whatsapp_business_messaging',
        'client_id' => env('META_APP_ID'),
        'client_secret' => env('META_APP_SECRET'),
        'redirect_uri' => env('META_WHATSAPP_REDIRECT_URI'),
    ],

    'linkedin' => [
        'label' => 'LinkedIn',
        'authorize_url' => 'https://www.linkedin.com/oauth/v2/authorization',
        'token_url' => 'https://www.linkedin.com/oauth/v2/accessToken',
        'scope' => 'w_member_social,r_liteprofile',
        'client_id' => env('LINKEDIN_CLIENT_ID'),
        'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
        'redirect_uri' => env('LINKEDIN_REDIRECT_URI'),
    ],

    'gbp' => [
        'label' => 'Google Business Profile',
        'authorize_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
        'token_url' => 'https://oauth2.googleapis.com/token',
        // Single scope covers both APIs ChannelOAuthController::resolveGbpLocation()
        // calls (mybusinessaccountmanagement + mybusinessbusinessinformation)
        // and what GbpPostingService needs later (mybusiness.googleapis.com).
        'scope' => 'https://www.googleapis.com/auth/business.manage',
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect_uri' => env('GOOGLE_BUSINESS_REDIRECT_URI'),
    ],

];
