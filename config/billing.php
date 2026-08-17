<?php

// config/billing.php
// Keeps Stripe Price IDs out of Blade/controllers — swap plans by editing .env only,
// never by touching code. Create these three Prices in the Stripe dashboard first.

return [

    'stripe_key' => env('STRIPE_KEY'),
    'stripe_secret' => env('STRIPE_SECRET'),
    'stripe_webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),

    'trial_days' => (int) env('BILLING_TRIAL_DAYS', 14),

    'tiers' => [
        'starter' => [
            'name' => 'Starter',
            'price_display' => '£4.99',
            'stripe_price_id' => env('STRIPE_PRICE_STARTER'),
        ],
        'professional' => [
            'name' => 'Professional',
            'price_display' => '£14.99',
            'stripe_price_id' => env('STRIPE_PRICE_PROFESSIONAL'),
        ],
        'enterprise' => [
            'name' => 'Enterprise',
            'price_display' => 'Custom',
            'stripe_price_id' => null, // Enterprise is sales-assisted, not self-serve checkout
        ],
    ],

];
