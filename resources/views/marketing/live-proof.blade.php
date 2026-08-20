<x-layouts.app title="Live proof — BusinessBots">

    <section class="mx-auto max-w-[900px] px-6 pt-20 pb-16 text-center">
        <span class="inline-block px-3 py-1 bg-accent-muted text-accent-light rounded-full text-xs font-semibold tracking-wide">
            LIVE PROOF
        </span>
        <h1 class="mt-5 text-3xl md:text-4xl font-bold leading-tight">
            The numbers behind <span class="text-gradient-accent">the autopilot</span>
        </h1>
        <p class="mt-4 text-text-secondary text-base leading-relaxed">
            Aggregated, non-sensitive metrics from across the network.
        </p>

        @if ($stats)
            <div class="mt-10 grid grid-cols-2 md:grid-cols-4 gap-px bg-border rounded-xl overflow-hidden border border-border">
                @foreach ([
                    ['value' => $stats['revenue_this_month'], 'label' => 'Revenue this month'],
                    ['value' => $stats['reply_speed'], 'label' => 'Avg WhatsApp reply time'],
                    ['value' => $stats['posts_today'], 'label' => 'Posts published today'],
                    ['value' => $stats['days_no_human_reply'], 'label' => 'Days with 0 human replies needed'],
                ] as $stat)
                    <div class="bg-surface-secondary p-4 text-center">
                        <div class="text-xl font-bold text-text-primary">{{ $stat['value'] }}</div>
                        <div class="mt-1 text-[10px] text-text-muted tracking-wide uppercase">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>

            <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach ([
                    ['value' => $stats['replies_count'], 'label' => 'AI replies sent'],
                    ['value' => $stats['posts_count'], 'label' => 'Posts published all-time'],
                    ['value' => $stats['leads_count'], 'label' => 'Leads captured'],
                ] as $stat)
                    <div class="bg-surface border border-border rounded-xl p-5 text-center">
                        <div class="text-2xl font-bold text-success">{{ $stat['value'] }}</div>
                        <div class="mt-1 text-xs text-text-muted">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 bg-surface border border-border rounded-xl p-5 flex items-center justify-between">
                <div class="text-sm text-text-secondary">Estimated total hours saved</div>
                <div class="text-2xl font-bold text-text-primary">{{ number_format($stats['total_saved']) }} hrs</div>
            </div>
        @else
            <div class="mt-10 mx-auto max-w-md bg-surface border border-border rounded-xl p-6">
                <p class="text-sm text-text-secondary">Stats are temporarily unavailable. Check back soon.</p>
            </div>
        @endif
    </section>

</x-layouts.app>