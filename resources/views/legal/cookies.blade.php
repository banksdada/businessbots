<x-layouts.app title="Cookie Policy — BusinessBots">
    <div class="mx-auto max-w-[720px] px-6 py-16 text-sm text-text-secondary leading-relaxed">

        <div class="mb-8 px-4 py-3 bg-warning/10 border border-warning/20 rounded-lg text-xs text-warning">
            Draft template — confirm actual cookies in use (analytics, etc.) once added, and connect a real consent banner before launch. Currently only strictly-necessary cookies are used.
        </div>

        <h1 class="text-2xl font-bold text-text-primary mb-2">Cookie Policy</h1>
        <p class="text-xs text-text-muted mb-8">Last updated {{ $legal['last_updated'] }}</p>

        <p class="mb-4">
            This policy explains how {{ $legal['company_name'] }} uses cookies and similar
            technologies on {{ $legal['domain'] }}.
        </p>

        <h2 class="text-base font-semibold text-text-primary mt-8 mb-3">Strictly necessary cookies</h2>
        <p class="mb-4">
            These cookies are required for the Service to function and cannot be switched off. No
            consent banner is required for these under UK PECR / EU ePrivacy rules.
        </p>
        <div class="border border-border rounded-lg overflow-hidden mb-4">
            <div class="grid grid-cols-3 px-4 py-2 bg-surface-secondary text-[10px] font-medium text-text-muted uppercase tracking-wide">
                <div>Cookie</div><div>Purpose</div><div>Duration</div>
            </div>
            <div class="grid grid-cols-3 px-4 py-2.5 border-t border-border text-xs">
                <div class="text-text-primary font-mono">businessbots_session</div>
                <div>Keeps you logged in, protects against CSRF</div>
                <div>Session</div>
            </div>
            <div class="grid grid-cols-3 px-4 py-2.5 border-t border-border text-xs">
                <div class="text-text-primary font-mono">XSRF-TOKEN</div>
                <div>Laravel's CSRF protection</div>
                <div>Session</div>
            </div>
        </div>

        <h2 class="text-base font-semibold text-text-primary mt-8 mb-3">Analytics cookies</h2>
        <p class="mb-4">
            Not currently in use. If added in future (e.g. product analytics), this policy and an
            on-site consent banner will be updated first, and consent will be requested before any
            such cookie is set — not after.
        </p>

        <h2 class="text-base font-semibold text-text-primary mt-8 mb-3">Third-party cookies</h2>
        <p class="mb-4">
            Stripe Checkout may set its own cookies during the payment flow, governed by
            <a href="https://stripe.com/cookies-policy/legal" class="text-accent-light">Stripe's Cookie Policy</a>.
            We don't control these directly.
        </p>

        <h2 class="text-base font-semibold text-text-primary mt-8 mb-3">Managing cookies</h2>
        <p class="mb-4">
            You can control or delete cookies through your browser settings. Blocking strictly
            necessary cookies will prevent you from logging in or using the Service.
        </p>

        <h2 class="text-base font-semibold text-text-primary mt-8 mb-3">Contact</h2>
        <p class="mb-4">
            Questions: <a href="mailto:{{ $legal['privacy_email'] }}" class="text-accent-light">{{ $legal['privacy_email'] }}</a>
        </p>

    </div>
</x-layouts.app>
