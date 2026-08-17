<section class="mx-auto max-w-[900px] px-6 pb-24">
    <div class="text-center mb-10">
        <span class="text-xs font-semibold text-accent-light tracking-wide">BUSINESSBOTS PRICING</span>
        <h2 class="mt-2 text-xl font-bold">Pay monthly. AI does the work.</h2>
    </div>

    <div class="grid md:grid-cols-3 gap-4">
        @foreach ($pricingTiers as $tier)
            <div class="relative bg-surface border rounded-xl p-6
                {{ $tier['featured'] ? 'border-border-accent bg-surface-elevated' : 'border-border' }}">

                @if ($tier['featured'])
                    <span class="absolute -top-2.5 left-4 bg-accent text-white text-[10px] font-semibold px-2.5 py-0.5 rounded-full">
                        Most popular
                    </span>
                @endif

                <div class="text-sm font-semibold text-text-secondary mt-1">{{ $tier['name'] }}</div>
                <div class="mt-2 text-2xl font-bold text-text-primary">
                    {{ $tier['price'] }}
                    @if ($tier['price'] !== 'Custom')
                        <span class="text-xs font-medium text-text-muted">/mo</span>
                    @endif
                </div>
                <div class="mt-1 text-xs text-text-muted">{{ $tier['subtitle'] }}</div>

                <ul class="mt-4 space-y-2 text-xs text-text-secondary">
                    @foreach ($tier['features'] as $feature)
                        <li>✓ {{ $feature }}</li>
                    @endforeach
                </ul>

                <a href="{{ $tier['cta_url'] }}"
                    class="mt-5 block text-center py-2.5 rounded-md text-xs font-semibold
                    {{ $tier['featured'] ? 'bg-gradient-accent text-white' : 'border border-border-strong text-text-primary' }}">
                    {{ $tier['cta_label'] }}
                </a>
            </div>
        @endforeach
    </div>

    <p class="mt-4 text-[11px] text-text-faint text-center">
        14-day free trial, no card required · Compare: enterprise care CRMs start at £150+/month
    </p>
</section>
