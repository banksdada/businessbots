<x-layouts.app title="Privacy Policy — BusinessBots">
    <div class="mx-auto max-w-[720px] px-6 py-16 text-sm text-text-secondary leading-relaxed">

        <div class="mb-8 px-4 py-3 bg-warning/10 border border-warning/20 rounded-lg text-xs text-warning">
            Draft template — not reviewed by a solicitor. UK GDPR / EU GDPR obligations are jurisdiction-specific; have this reviewed before processing real customer or lead data.
        </div>

        <h1 class="text-2xl font-bold text-text-primary mb-2">Privacy Policy</h1>
        <p class="text-xs text-text-muted mb-8">Last updated {{ $legal['last_updated'] }}</p>

        <p class="mb-4">
            {{ $legal['company_name'] }} ("we", "us") is the data controller for personal data
            collected through the BusinessBots platform at {{ $legal['domain'] }}. This policy
            explains what we collect, why, and your rights under UK GDPR / EU GDPR.
        </p>

        <h2 class="text-base font-semibold text-text-primary mt-8 mb-3">1. What we collect</h2>
        <p class="mb-2 font-medium text-text-primary text-xs uppercase tracking-wide">From you (the business owner)</p>
        <ul class="list-disc pl-5 mb-4 space-y-1">
            <li>Account details: name, email, password (hashed, never stored in plain text)</li>
            <li>Business details: business name, location, description, industry</li>
            <li>Billing details: handled directly by Stripe — we store a Stripe customer reference, not card numbers</li>
            <li>OAuth tokens for connected channels (WhatsApp, Instagram, LinkedIn, Google Business Profile), encrypted at rest</li>
        </ul>
        <p class="mb-2 font-medium text-text-primary text-xs uppercase tracking-wide">From your leads and customers (processed on your behalf)</p>
        <ul class="list-disc pl-5 mb-4 space-y-1">
            <li>Phone numbers and message content sent via WhatsApp</li>
            <li>Names, where provided in conversation</li>
            <li>Engagement data from social posts (aggregate metrics only — not individual follower data)</li>
        </ul>
        <p class="mb-4 text-xs">
            For this category, you (the business) are the data controller and we act as your data
            processor. A Data Processing Agreement is available on request —
            <a href="mailto:{{ $legal['legal_email'] }}" class="text-accent-light">{{ $legal['legal_email'] }}</a>.
        </p>

        <h2 class="text-base font-semibold text-text-primary mt-8 mb-3">2. Why we process this data</h2>
        <ul class="list-disc pl-5 mb-4 space-y-1">
            <li><strong class="text-text-primary font-medium">Contract performance</strong> — providing the Service you've subscribed to</li>
            <li><strong class="text-text-primary font-medium">Legitimate interest</strong> — service improvement, fraud prevention, security monitoring</li>
            <li><strong class="text-text-primary font-medium">Legal obligation</strong> — tax and accounting records</li>
            <li><strong class="text-text-primary font-medium">Consent</strong> — marketing communications, which you can opt out of at any time</li>
        </ul>

        <h2 class="text-base font-semibold text-text-primary mt-8 mb-3">3. Who we share data with</h2>
        <p class="mb-4">
            We share data with the following sub-processors, each engaged under appropriate
            safeguards (Standard Contractual Clauses where data leaves the UK/EU):
        </p>
        <div class="border border-border rounded-lg overflow-hidden mb-4">
            <div class="grid grid-cols-3 px-4 py-2 bg-surface-secondary text-[10px] font-medium text-text-muted uppercase tracking-wide">
                <div>Processor</div><div>Purpose</div><div>Location</div>
            </div>
            @foreach ($legal['sub_processors'] as $processor)
                <div class="grid grid-cols-3 px-4 py-2.5 border-t border-border text-xs">
                    <div class="text-text-primary">{{ $processor['name'] }}</div>
                    <div>{{ $processor['purpose'] }}</div>
                    <div>{{ $processor['location'] }}</div>
                </div>
            @endforeach
        </div>
        <p class="mb-4">We do not sell personal data to third parties.</p>

        <h2 class="text-base font-semibold text-text-primary mt-8 mb-3">4. How long we keep data</h2>
        <p class="mb-4">
            Account and business data is retained for the life of your subscription plus 90 days
            after cancellation, after which it is deleted unless a longer period is required for
            legal or tax purposes. Lead/conversation data follows the same retention unless you
            request earlier deletion.
        </p>

        <h2 class="text-base font-semibold text-text-primary mt-8 mb-3">5. Your rights</h2>
        <p class="mb-2">Under UK/EU GDPR, you have the right to:</p>
        <ul class="list-disc pl-5 mb-4 space-y-1">
            <li>Access the personal data we hold about you</li>
            <li>Correct inaccurate data (editable directly in Settings for most fields)</li>
            <li>Request deletion of your account and associated data</li>
            <li>Export your data in a portable format</li>
            <li>Object to or restrict certain processing</li>
            <li>Withdraw consent for marketing communications at any time</li>
        </ul>
        <p class="mb-4">
            To exercise any of these rights, email
            <a href="mailto:{{ $legal['privacy_email'] }}" class="text-accent-light">{{ $legal['privacy_email'] }}</a>.
            We respond within 30 days as required by law.
        </p>

        <h2 class="text-base font-semibold text-text-primary mt-8 mb-3">6. Security</h2>
        <p class="mb-4">
            OAuth tokens and payment credentials are encrypted at rest. Access to production data is
            restricted to authorized personnel. We recommend enabling a strong, unique password for
            your account.
        </p>

        <h2 class="text-base font-semibold text-text-primary mt-8 mb-3">7. Cookies</h2>
        <p class="mb-4">
            See our <a href="{{ route('legal.cookies') }}" class="text-accent-light">Cookie Policy</a>
            for details on cookies and similar technologies used on this site.
        </p>

        <h2 class="text-base font-semibold text-text-primary mt-8 mb-3">8. Supervisory authority</h2>
        <p class="mb-4">
            If you're unsatisfied with how we've handled your data, you have the right to lodge a
            complaint with the UK Information Commissioner's Office (ico.org.uk) or your local EU
            data protection authority.
        </p>

        <h2 class="text-base font-semibold text-text-primary mt-8 mb-3">9. Contact</h2>
        <p class="mb-4">
            Data protection queries: <a href="mailto:{{ $legal['privacy_email'] }}" class="text-accent-light">{{ $legal['privacy_email'] }}</a>
        </p>

    </div>
</x-layouts.app>
