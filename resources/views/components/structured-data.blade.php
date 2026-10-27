@php
    $domain = rtrim(config('legal.domain'), '/');
    $brand = config('legal.brand');
    $companyName = config('legal.company_name');

    $organization = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $brand,
        'legalName' => $companyName,
        'url' => $domain,
        'logo' => $domain . '/images/og-default.png',
        'description' => 'AI Operating System for small businesses — automates the repetitive work so owners can focus on revenue.',
        'areaServed' => 'GB',
        'sameAs' => [],
    ];

    $service = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => $brand . ' AI OS',
        'provider' => [
            '@type' => 'Organization',
            'name' => $brand,
        ],
        'description' => 'AI-driven automation for small businesses: social media content generation and scheduling, lead follow-up, and related analytics.',
        'offers' => [
            '@type' => 'Offer',
            'price' => '4.99',
            'priceCurrency' => 'GBP',
            'priceSpecification' => [
                '@type' => 'UnitPriceSpecification',
                'price' => '4.99',
                'priceCurrency' => 'GBP',
                'unitCode' => 'MON',
            ],
        ],
    ];

    $faqPage = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => [
            ['@type' => 'Question', 'name' => 'What does it cost?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => '£4.99/month. No setup fee, no lock-in. Cancel anytime.']],
            ['@type' => 'Question', 'name' => 'How long does setup take?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'About 15 minutes. Book a demo and we\'ll scope your workflow first.']],
            ['@type' => 'Question', 'name' => 'Does it replace my staff?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'It handles the repetitive work — replies, posting, follow-ups — so your team focuses on revenue.']],
            ['@type' => 'Question', 'name' => 'Is my data safe?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Yes. GDPR-aware, encrypted tokens, opt-out (STOP) and opt-in (START) built in.']],
            ['@type' => 'Question', 'name' => 'What industries does it work for?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => '8 verticals seeded and counting. If yours isn\'t listed, ask — we\'ll work it out.']],
        ],
    ];
@endphp

<script type="application/ld+json">{!! json_encode($organization, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
@if (config('features.social') && request()->routeIs('home'))
<script type="application/ld+json">{!! json_encode($service, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
<script type="application/ld+json">{!! json_encode($faqPage, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
@endif