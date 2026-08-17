<x-layouts.app title="Terms of Service — BusinessBots">
    <div class="mx-auto max-w-[720px] px-6 py-16 text-sm text-text-secondary leading-relaxed">

        <div class="mb-8 px-4 py-3 bg-warning/10 border border-warning/20 rounded-lg text-xs text-warning">
            Draft template — not reviewed by a solicitor. Do not launch to paying customers until this has had legal review appropriate for your jurisdiction.
        </div>

        <h1 class="text-2xl font-bold text-text-primary mb-2">Terms of Service</h1>
        <p class="text-xs text-text-muted mb-8">Last updated {{ $legal['last_updated'] }}</p>

        <p class="mb-4">
            These Terms of Service ("Terms") govern access to and use of the BusinessBots platform
            (the "Service"), operated by {{ $legal['company_name'] }} ("we", "us", "our"), a company
            with its registered address at {{ $legal['company_address'] }}. By creating an account or
            using the Service, you agree to these Terms.
        </p>

        <h2 class="text-base font-semibold text-text-primary mt-8 mb-3">1. The Service</h2>
        <p class="mb-4">
            BusinessBots provides AI-driven automation for small businesses, including WhatsApp lead
            capture and reply, social media content generation and scheduling, and related analytics
            ("the Service"). The Service is provided on a subscription basis as described at
            <a href="{{ route('marketing.pricing') }}" class="text-accent-light">{{ $legal['domain'] }}/pricing</a>.
        </p>

        <h2 class="text-base font-semibold text-text-primary mt-8 mb-3">2. Accounts and eligibility</h2>
        <p class="mb-4">
            You must be at least 18 years old and have authority to bind the business you register
            on behalf of. You are responsible for maintaining the confidentiality of your account
            credentials and for all activity under your account.
        </p>

        <h2 class="text-base font-semibold text-text-primary mt-8 mb-3">3. Subscriptions, trials, and billing</h2>
        <p class="mb-4">
            Paid plans are billed monthly in advance via Stripe. New accounts receive a
            {{ config('billing.trial_days') }}-day free trial; no payment method is required to start
            a trial. You may cancel at any time via your billing portal — cancellation takes effect
            at the end of the current billing period, and no partial refunds are issued for unused
            time within a period unless required by applicable law.
        </p>

        <h2 class="text-base font-semibold text-text-primary mt-8 mb-3">4. Acceptable use</h2>
        <p class="mb-4">You agree not to use the Service to:</p>
        <ul class="list-disc pl-5 mb-4 space-y-1">
            <li>Send unsolicited messages to individuals who have not consented to contact ("spam"), including via WhatsApp, in violation of Meta's WhatsApp Business Messaging Policy</li>
            <li>Generate or distribute unlawful, deceptive, defamatory, or infringing content</li>
            <li>Attempt to reverse-engineer, resell, or white-label the Service without a separate written agreement</li>
            <li>Interfere with the Service's operation or attempt unauthorized access to other accounts</li>
        </ul>

        <h2 class="text-base font-semibold text-text-primary mt-8 mb-3">5. Your data and content</h2>
        <p class="mb-4">
            You retain ownership of the business data, lead information, and content you upload or
            generate through the Service. You grant us a limited license to process this data solely
            to provide the Service, as described in our
            <a href="{{ route('legal.privacy') }}" class="text-accent-light">Privacy Policy</a>.
            You are responsible for ensuring you have lawful basis (including WhatsApp opt-in consent
            where applicable) to contact the leads and customers you manage through the Service.
        </p>

        <h2 class="text-base font-semibold text-text-primary mt-8 mb-3">6. AI-generated content</h2>
        <p class="mb-4">
            The Service uses third-party AI models (including OpenAI) to generate message replies and
            social media content on your behalf. AI-generated content may contain errors; you are
            responsible for reviewing content before it materially affects a customer relationship or
            legal obligation, and we recommend periodic manual review of AI-sent replies.
        </p>

        <h2 class="text-base font-semibold text-text-primary mt-8 mb-3">7. Third-party platforms</h2>
        <p class="mb-4">
            The Service integrates with WhatsApp, Instagram, LinkedIn, and Google Business Profile.
            Your use of these integrations is also subject to each platform's own terms. We are not
            responsible for outages, policy changes, or account actions taken by these third-party
            platforms.
        </p>

        <h2 class="text-base font-semibold text-text-primary mt-8 mb-3">8. Limitation of liability</h2>
        <p class="mb-4">
            To the maximum extent permitted by law, {{ $legal['company_name'] }} is not liable for
            indirect, incidental, or consequential damages arising from use of the Service, including
            lost profits or lost leads. Our total liability for any claim is limited to the amount you
            paid for the Service in the 12 months preceding the claim.
        </p>

        <h2 class="text-base font-semibold text-text-primary mt-8 mb-3">9. Termination</h2>
        <p class="mb-4">
            We may suspend or terminate accounts that violate these Terms, including sustained
            non-payment or misuse of WhatsApp/social integrations that puts our platform access at
            risk. You may close your account at any time from Settings.
        </p>

        <h2 class="text-base font-semibold text-text-primary mt-8 mb-3">10. Changes to these Terms</h2>
        <p class="mb-4">
            We may update these Terms from time to time. Material changes will be notified by email
            or in-app notice at least 14 days before taking effect.
        </p>

        <h2 class="text-base font-semibold text-text-primary mt-8 mb-3">11. Contact</h2>
        <p class="mb-4">
            Questions about these Terms: <a href="mailto:{{ $legal['legal_email'] }}" class="text-accent-light">{{ $legal['legal_email'] }}</a>
        </p>

    </div>
</x-layouts.app>
