<?php

// config/legal.php
// Single source of truth for company/contact details used across ToS, Privacy,
// and Cookie pages. Update .env once the real domain/entity is confirmed —
// never hardcode these values directly in Blade views.

return [
    'company_name' => env('COMPANY_LEGAL_NAME', 'BusinessBots Ltd'),
    'company_address' => env('COMPANY_ADDRESS', 'Address to be confirmed'),
    'domain' => env('APP_URL', 'https://automation.baseuse.xyz'),

    'legal_email' => env('LEGAL_CONTACT_EMAIL', 'legal@automation.baseuse.xyz'),
    'privacy_email' => env('PRIVACY_CONTACT_EMAIL', 'privacy@automation.baseuse.xyz'),
    'support_email' => env('SUPPORT_CONTACT_EMAIL', 'support@automation.baseuse.xyz'),

    // Sub-processors — anyone who touches customer/lead personal data on our behalf.
    // Update this list whenever a new integration is added (see architecture.md).
    'sub_processors' => [
        ['name' => 'Meta Platforms, Inc.', 'purpose' => 'WhatsApp Business API, Instagram posting', 'location' => 'USA/EU (SCCs)'],
        ['name' => 'OpenAI, L.L.C.', 'purpose' => 'AI content generation, reply drafting', 'location' => 'USA (SCCs)'],
        ['name' => 'Stripe, Inc.', 'purpose' => 'Payment processing, billing', 'location' => 'USA/EU (SCCs)'],
        ['name' => 'LinkedIn Corporation', 'purpose' => 'LinkedIn posting', 'location' => 'USA (SCCs)'],
        ['name' => 'Google LLC', 'purpose' => 'Google Business Profile posting, OAuth login, trend data', 'location' => 'USA/EU (SCCs)'],
        ['name' => 'Hosting provider (Coolify/DigitalOcean or equivalent)', 'purpose' => 'Application hosting, database', 'location' => 'To be confirmed'],
    ],

    'last_updated' => '2026-08-17',
];
