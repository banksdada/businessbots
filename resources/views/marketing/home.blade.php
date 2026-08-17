<x-layouts.app title="BusinessBots — Your business, running on autopilot">

    {{-- Hero --}}
    <section class="mx-auto max-w-[720px] px-6 pt-20 pb-16 text-center">
        <span class="inline-block px-3 py-1 bg-accent-muted text-accent-light rounded-full text-xs font-semibold tracking-wide">
            {{ $liveBusinessCount ?? 8 }} BUSINESSES RUNNING NOW · 0 HUMAN STAFF
        </span>

        <h1 class="mt-5 text-3xl md:text-4xl font-bold leading-tight">
            One AI system.<br>
            <span class="text-gradient-accent">Any small business.</span><br>
            £4.99/Month.
        </h1>

        <p class="mt-4 text-text-secondary text-base leading-relaxed">
            Runs WhatsApp, content and lead follow-up 24/7 — while the owner works 15 minutes a day.
        </p>

        <div class="mt-8 flex justify-center gap-3">
            <a href="{{ route('live-proof') }}" class="px-5 py-2.5 bg-gradient-accent text-white rounded-md text-sm font-semibold">
                View live proof dashboard
            </a>
            <a href="{{ route('marketing.demo') }}" class="px-5 py-2.5 border border-border-strong text-text-primary rounded-md text-sm font-semibold">
                Book a demo
            </a>
        </div>
    </section>

    {{-- Stat bar --}}
    <section class="mx-auto max-w-[720px] px-6 pb-20">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-px bg-border rounded-xl overflow-hidden border border-border">
            @foreach ([
                ['value' => $stats['agents_active'] ?? '27+', 'label' => 'AI agents active'],
                ['value' => $stats['businesses_running'] ?? '8', 'label' => 'Businesses running'],
                ['value' => $stats['avg_reply_time'] ?? '<60s', 'label' => 'WhatsApp reply time'],
                ['value' => '£0', 'label' => 'Human salary cost'],
            ] as $stat)
                <div class="bg-surface-secondary p-4 text-center">
                    <div class="text-xl font-bold text-text-primary">{{ $stat['value'] }}</div>
                    <div class="mt-1 text-[10px] text-text-muted tracking-wide uppercase">{{ $stat['label'] }}</div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Vertical picker teaser --}}
    <section class="mx-auto max-w-[720px] px-6 pb-20">
        <h2 class="text-lg font-semibold text-center mb-6">Works for any business, out of the box</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach ($verticals as $vertical)
                <a href="{{ route('marketing.industries', $vertical->slug) }}"
                    class="bg-surface border border-border rounded-lg p-3 text-center text-sm font-medium text-text-secondary hover:border-border-accent hover:text-text-primary transition-colors">
                    {{ $vertical->label }}
                </a>
            @endforeach
        </div>
    </section>

    @include('marketing.partials.pricing-cards')

</x-layouts.app>
