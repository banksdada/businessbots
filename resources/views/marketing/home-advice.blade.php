<x-layouts.app :title="config('app.name') . ' — Practical advice for small organisations'">

    {{-- Hero --}}
    <section class="bg-surface border-b border-border">
        <div class="mx-auto max-w-[1100px] px-4 sm:px-6 py-16 sm:py-24 grid md:grid-cols-2 gap-10 items-center">
            <div>
                <span class="inline-block px-3 py-1 bg-accent-muted text-accent-dark rounded-full text-sm font-semibold">
                    For care providers, churches and small firms
                </span>
                <h1 class="mt-5 text-4xl sm:text-5xl font-bold leading-tight tracking-tight">
                    Tell us the problem.<br>
                    <span class="text-gradient-accent">Get a clear plan to fix it.</span>
                </h1>
                <p class="mt-5 text-lg text-text-secondary leading-relaxed">
                    Describe what's slowing your organisation down. You'll get a written report with practical next steps, drafted with AI and checked by a person before you see it.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('register') }}" class="btn-primary text-base px-6 py-3">Get started</a>
                    <a href="#how-it-works" class="btn-secondary text-base px-6 py-3">How it works</a>
                </div>
            </div>

            <div class="card p-6 bg-surface-secondary" aria-hidden="true">
                <p class="text-sm font-semibold text-text-muted">Example request</p>
                <p class="mt-2 font-semibold">"Staff rotas take us a full day every week"</p>
                <div class="mt-5 rounded-lg bg-surface border border-border p-4 space-y-2 text-sm">
                    <p class="font-semibold">Your report</p>
                    <p class="text-text-secondary">1. Where the time goes today</p>
                    <p class="text-text-secondary">2. Three options, from free to paid</p>
                    <p class="text-text-secondary">3. A first step you can take this week</p>
                </div>
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section id="how-it-works" class="mx-auto max-w-[1100px] px-4 sm:px-6 py-16 sm:py-20 scroll-mt-20">
        <h2 class="text-2xl sm:text-3xl font-bold text-center">How it works</h2>
        <ol class="mt-10 grid md:grid-cols-3 gap-5">
            @foreach ([
                ['1', 'Describe the problem', 'Answer a few simple questions about your organisation and what\'s getting in the way. It takes about five minutes.'],
                ['2', 'We prepare your report', 'AI drafts advice for your sector and size. A person then checks and improves it.'],
                ['3', 'Read and act', 'We email you when your report is ready. If you want, we can help you build the solution too.'],
            ] as [$n, $heading, $text])
                <li class="card p-6">
                    <span class="inline-flex w-9 h-9 rounded-full bg-accent-muted text-accent-dark font-bold items-center justify-center">{{ $n }}</span>
                    <h3 class="mt-4 text-lg font-semibold">{{ $heading }}</h3>
                    <p class="mt-2 text-text-secondary leading-relaxed">{{ $text }}</p>
                </li>
            @endforeach
        </ol>
    </section>

    {{-- Who it's for --}}
    <section class="bg-surface border-y border-border">
        <div class="mx-auto max-w-[1100px] px-4 sm:px-6 py-16 sm:py-20">
            <h2 class="text-2xl sm:text-3xl font-bold text-center">Made for organisations like yours</h2>
            <div class="mt-10 grid sm:grid-cols-3 gap-5">
                @foreach ([
                    ['Care providers', 'Rotas, incident tracking, paperwork and CQC evidence.'],
                    ['Churches and charities', 'Volunteers, communications, giving and events.'],
                    ['Small businesses', 'Missed enquiries, admin, follow-ups and reporting.'],
                ] as [$heading, $text])
                    <div class="rounded-xl border border-border p-6">
                        <h3 class="font-semibold text-lg">{{ $heading }}</h3>
                        <p class="mt-2 text-text-secondary">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Call to action --}}
    <section class="mx-auto max-w-[760px] px-4 sm:px-6 py-16 sm:py-20 text-center">
        <h2 class="text-2xl sm:text-3xl font-bold">Ready to ask your first question?</h2>
        <p class="mt-3 text-lg text-text-secondary">Sign up with just your email. No password needed.</p>
        <a href="{{ route('register') }}" class="btn-primary mt-8 text-base px-6 py-3">Get started</a>
    </section>

</x-layouts.app>
