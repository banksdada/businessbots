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
            Runs content, lead follow-up and reporting 24/7 — while the owner works 15 minutes a day.
        </p>

        <div class="mt-8 flex justify-center gap-3">
            <a href="{{ route('live-proof') }}" class="px-5 py-2.5 bg-accent text-white rounded-md text-sm font-semibold">
                View live proof dashboard
            </a>
            <a href="{{ route('marketing.demo') }}" class="px-5 py-2.5 border border-border-strong text-text-primary rounded-md text-sm font-semibold">
                Book a demo
            </a>
        </div>

        <div class="mt-4 flex flex-wrap justify-center gap-3 text-xs text-text-muted">
            <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-success"></span>Takes 2 minutes</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-success"></span>No card needed</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-success"></span>A real person checks every plan</span>
        </div>
    </section>

    {{-- AIOS homepage section (bold) --}}
    <section class="mx-auto max-w-[720px] px-6 pb-24">
        <div class="bg-surface border border-accent rounded-xl p-8 md:p-10">
            <span class="text-label inline-block px-2.5 py-0.5 bg-accent-muted text-accent rounded-full">AI‑OS</span>
            <h2 class="mt-3 text-h2">We don't just fix your problem.<br><span class="text-accent-dark">We run the system behind it.</span></h2>
            <p class="mt-4 text-text-secondary leading-relaxed">
                The AIOS (AI Operating System) automates repetitive work—email, content, follow‑ups, payment chasing—so you can focus on revenue. It powers the solutions you see on this site.
            </p>
            <ul class="mt-4 space-y-2 list-disc list-inside text-sm text-text-secondary">
                <li>Less manual admin, more selling time</li>
                <li>Leads captured and followed up automatically</li>
                <li>Content produced consistently without hiring</li>
                <li>Payments chased on autopilot</li>
                <li>Everything auditable — you stay in control</li>
            </ul>
            <div class="mt-6">
                <a href="#solutions" class="inline-block px-5 py-2.5 bg-gradient-accent text-white rounded-md text-sm font-semibold hover:opacity-90 transition-opacity">
                    See how it works
                </a>
            </div>
        </div>
    </section>

    <!-- Stat bar -->
    <section class="mx-auto max-w-[720px] px-6 pb-20">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-px bg-border rounded-xl overflow-hidden border border-border">
            @foreach ([
                ['value' => $stats['agents_active'] ?? '27+', 'label' => 'AI agents active'],
                ['value' => $stats['businesses_running'] ?? '8', 'label' => 'Businesses running'],
                ['value' => $stats['leads_captured'] ?? '0', 'label' => 'Leads captured'],
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

    {{-- Solutions delivered --}}
    <section class="mx-auto max-w-[720px] px-6 pb-20">
        <div class="text-center mb-8">
            <span class="text-label inline-block px-2.5 py-0.5 bg-accent-muted text-accent rounded-full">SOLUTIONS DELIVERED</span>
            <h2 class="mt-2 text-h2 text-text-primary">Real businesses, real numbers</h2>
            <p class="mt-2 text-sm text-text-secondary">Here's what running on autopilot looks like.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ([
                ['title' => 'Content creation', 'body' => 'Consistent posts across Instagram, LinkedIn, GBP from one brief.'],
                ['title' => 'Lead capture & follow-up', 'body' => 'Leads captured, followed up, routed to you automatically.'],
                ['title' => 'Payment-chasing workflows', 'body' => 'Automated reminders. Faster cash collection.'],
                ['title' => 'Reporting & visibility', 'body' => 'Dashboard of what\'s working. Decisions on data, not gut.'],
            ] as $solution)
                <div class="bg-surface border border-border rounded-xl p-5 hover:border-border-accent transition-colors">
                    <h3 class="text-sm font-semibold text-text-primary">{{ $solution['title'] }}</h3>
                    <p class="mt-2 text-sm text-text-secondary leading-relaxed">{{ $solution['body'] }}</p>
                    <a href="{{ route('live-proof') }}" class="mt-3 inline-block text-xs text-accent-light hover:text-accent">See live proof →</a>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Trust + friction reducers --}}
    <section class="mx-auto max-w-[720px] px-6 pb-16">
        <div class="flex flex-wrap justify-center gap-4 text-xs text-text-muted">
            <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-success"></span>0 human staff — AI agents run 24/7</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-success"></span">£4.99/month — no setup fee, no lock-in</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-success"></span>GDPR-aware, clear opt-outs built in</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-success"></span>Takes 15 minutes to set up</span>
        </div>
    </section>

    {{-- FAQ block --}}
    <section class="mx-auto max-w-[720px] px-6 pb-20">
        <h2 class="text-h2 text-center mb-6">Frequently asked questions</h2>
        <div class="space-y-4">
            @foreach ([
                ['q' => 'What does it cost?', 'a' => '£4.99/month. No setup fee, no lock-in. Cancel anytime.'],
                ['q' => 'How long does setup take?', 'a' => 'About 15 minutes. Book a demo and we\'ll scope your workflow first.'],
                ['q' => 'Does it replace my staff?', 'a' => 'It handles the repetitive work — replies, posting, follow-ups — so your team focuses on revenue.'],
                ['q' => 'Is my data safe?', 'a' => 'Yes. GDPR-aware, encrypted tokens, and clear opt-outs built in.'],
                ['q' => 'What industries does it work for?', 'a' => '8 verticals seeded and counting. If yours isn\'t listed, ask — we\'ll work it out.'],
            ] as $faq)
                <details class="bg-surface border border-border rounded-xl p-5">
                    <summary class="text-sm font-semibold text-text-primary cursor-pointer">{{ $faq['q'] }}</summary>
                    <p class="mt-2 text-sm text-text-secondary leading-relaxed">{{ $faq['a'] }}</p>
                </details>
            @endforeach
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

        <p class="mt-6 text-center text-sm">
            <a href="{{ route('insights.index') }}" class="text-accent-light hover:text-accent">
                Read the latest insights →
            </a>
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
