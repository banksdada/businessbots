<x-layouts.app :title="config('app.name') . ' — Your business, running on autopilot'">

    {{-- Hero --}}
    <section class="mx-auto max-w-[720px] px-6 pt-20 pb-16 text-center">
        <span class="text-label inline-block px-3 py-1 bg-accent-muted text-accent rounded-full">
            {{ $liveBusinessCount ?? 8 }} businesses running now · 0 human staff
        </span>

        <h1 class="mt-5 text-display">
            One AI system.<br>
            <span class="text-accent-dark">Any small business.</span><br>
            £4.99/Month.
        </h1>

        <p class="mt-4 text-text-secondary text-body leading-relaxed">
            Runs WhatsApp, content and lead follow-up 24/7 — while the owner works 15 minutes a day.
        </p>

        <div class="mt-8 flex justify-center gap-3">
            <a href="{{ route('live-proof') }}" class="px-5 py-2.5 bg-accent text-white rounded-md text-sm font-semibold">
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
                    <div class="mt-1 text-label">{{ $stat['label'] }}</div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Vertical picker teaser --}}
    <section class="mx-auto max-w-[720px] px-6 pb-20">
        <h2 class="text-h2 text-center mb-6">Works for any business, out of the box</h2>
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

    {{-- AI‑OS native: the fix plan, running on autopilot --}}
    <section class="mx-auto max-w-[720px] px-6 pb-24">
        <div class="bg-surface border border-border-accent rounded-xl p-8 md:p-10">
            <span class="text-label inline-block px-2.5 py-0.5 bg-accent-muted text-accent rounded-full">AI‑OS NATIVE</span>
            <h2 class="mt-3 text-h2">
                The fix plan isn't a PDF.<br>
                <span class="text-accent-dark">It runs.</span>
            </h2>
            <p class="mt-4 text-text-secondary leading-relaxed">
                Describe the problem, get a clear fix plan — and then have that
                plan actually execute. Your AI OS handles the follow‑up, the
                posting and the reporting, on a monthly basis, while you keep
                doing the work you're good at.
            </p>

            <div class="mt-6 grid sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <div class="text-lg font-bold text-text-primary">1</div>
                    <p class="text-text-secondary">Describe the problem in plain English.</p>
                </div>
                <div>
                    <div class="text-lg font-bold text-text-primary">2</div>
                    <p class="text-text-secondary">We map it to an automation that runs weekly.</p>
                </div>
                <div>
                    <div class="text-lg font-bold text-text-primary">3</div>
                    <p class="text-text-secondary">You get a report, not a todo list.</p>
                </div>
            </div>

            <div class="mt-7 flex flex-wrap items-center gap-3">
                <a href="{{ route('marketing.demo') }}"
                   class="px-5 py-2.5 bg-gradient-accent text-white rounded-md text-sm font-semibold hover:opacity-90 transition-opacity">
                    Book a 15‑minute integration call
                </a>
                <p class="text-xs text-text-muted">
                    No quote until we've scoped your workflow.
                </p>
            </div>
        </div>
    </section>

    {{-- AI insights: what the system is seeing right now --}}
    <section class="mx-auto max-w-[720px] px-6 pb-28">
        <div class="flex items-center gap-2 mb-5">
            <span class="w-2 h-2 rounded-full bg-success animate-pulse"></span>
            <h2 class="text-h2">AI insights, live</h2>
            <span class="text-label">updated as the system runs</span>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            @foreach ($aiInsights as $insight)
                <div class="bg-surface border border-border rounded-xl p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-label">{{ $insight['label'] }}</span>
                        <span class="text-[10px] text-text-faint">{{ $insight['as_of'] }}</span>
                    </div>
                    <p class="mt-2 text-2xl font-bold text-text-primary">{{ $insight['value'] }}</p>
                    <p class="mt-1 text-sm text-text-secondary leading-relaxed">{{ $insight['detail'] }}</p>
                </div>
            @endforeach
        </div>

        <p class="mt-4 text-xs text-text-faint text-center">
            These are the signals the AI OS watches. When you run your own, you get
            the same feed — about your leads, your channels, your replies.
        </p>
    </section>

    {{-- Success stories: real businesses, real numbers --}}
    <section class="mx-auto max-w-[1200px] px-6 pb-28">
        <div class="text-center mb-8">
            <span class="text-label inline-block px-2.5 py-0.5 bg-accent-muted text-accent rounded-full">OUTCOMES</span>
            <h2 class="mt-2 text-h2 text-text-primary">Businesses running on autopilot</h2>
        </div>

        @if (count($successStories) > 0)
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($successStories as $story)
                    <div class="group relative bg-surface border border-border rounded-xl p-5 hover:border-border-accent transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-md bg-accent flex items-center justify-center text-white font-semibold text-xs">
                                    {{ strtoupper(substr($story['name'], 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-text-primary leading-tight">{{ $story['name'] }}</div>
                                    <div class="text-[10px] text-text-muted">{{ $story['vertical'] }}</div>
                                </div>
                            </div>
                            <span class="text-[10px] text-text-faint">{{ $story['since'] }}</span>
                        </div>

                        <div class="mt-4 flex items-baseline gap-1">
                            <span class="text-3xl font-bold text-text-primary">{{ $story['metric'] }}</span>
                            <span class="text-xs text-text-muted">{{ $story['metric_label'] }}</span>
                        </div>

                        <div class="mt-3 flex items-center gap-3 text-[11px] text-text-muted">
                            <span class="inline-flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-success"></span>
                                {{ $story['posts'] }} {{ $story['posts'] === 1 ? 'post' : 'posts' }} this month
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-surface border border-dashed border-border-strong rounded-xl p-8 text-center">
                <p class="text-sm text-text-secondary">
                    First customer coming soon. <a href="{{ route('register') }}" class="text-accent-light hover:text-accent">Be the first</a>.
                </p>
            </div>
        @endif
    </section>

</x-layouts.app>
