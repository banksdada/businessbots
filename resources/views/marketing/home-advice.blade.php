<x-layouts.app :title="config('app.name') . ' — Practical fixes for any business or organisation'">

    {{-- Hero --}}
    <section class="overflow-hidden">
        <div class="mx-auto max-w-[1100px] px-4 sm:px-6 pt-12 pb-16 sm:pt-20 sm:pb-24 grid md:grid-cols-2 gap-12 items-center">
            <div>
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-surface border border-border rounded-full text-sm font-medium text-text-secondary">
                    <span class="w-2 h-2 rounded-full bg-sage-strong"></span>
                    For teams, startups and growing businesses
                </span>
                <h1 class="mt-6 text-4xl sm:text-5xl lg:text-[3.5rem] font-semibold leading-[1.08]">
                    Tell us what's getting in the way.
                    <span class="italic text-accent">We'll find the fix.</span>
                </h1>
                <p class="mt-6 text-lg text-text-secondary leading-relaxed max-w-[34rem]">
                    You don't need to know anything about AI or software. Describe the problem in your own words and you'll get a clear plan to fix it: what's causing it, the simplest fix, and what it could save you in time and money. A real person checks every plan before it reaches you.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('register') }}" class="btn-primary text-base px-7 py-3">Tell us your problem</a>
                    <a href="#how-it-works" class="btn-secondary text-base px-7 py-3">See how it works</a>
                </div>
                <p class="mt-5 text-sm text-text-muted">Sign up with just your email. No password to remember.</p>
            </div>

            {{-- People cluster --}}
            <div class="relative mx-auto w-full max-w-[460px] aspect-square" aria-hidden="true">
                <x-person name="manager-hijab" bg="bg-peach" class="absolute left-[18%] top-[6%] w-[58%] aspect-square ring-8 ring-background" />
                <x-person name="pastor-coffee" bg="bg-sage" class="absolute left-0 bottom-[4%] w-[40%] aspect-square ring-8 ring-background" />
                <x-person name="owner-explain" bg="bg-sun" class="absolute right-0 bottom-[10%] w-[38%] aspect-square ring-8 ring-background" />

                <div class="absolute -left-2 sm:left-0 top-[10%] card px-4 py-3 max-w-[13rem] -rotate-2">
                    <p class="text-sm font-medium leading-snug">"Our admin takes a whole day every week."</p>
                </div>
                <div class="absolute right-0 sm:-right-2 top-[44%] card px-4 py-3 flex items-center gap-3 rotate-2">
                    <span class="w-8 h-8 rounded-full bg-sage-strong flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <div>
                        <p class="text-sm font-semibold leading-tight">Your plan is ready</p>
                        <p class="text-xs text-text-muted">Checked by a person</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section id="how-it-works" class="bg-surface border-y border-border scroll-mt-20">
        <div class="mx-auto max-w-[1100px] px-4 sm:px-6 py-16 sm:py-20">
            <p class="text-center text-sm font-semibold text-peach-strong uppercase tracking-wider">How it works</p>
            <h2 class="mt-2 text-3xl sm:text-4xl font-semibold text-center">Three simple steps</h2>
            <ol class="mt-12 grid md:grid-cols-3 gap-6">
                @foreach ([
                    ['1', 'bg-peach', 'Tell us what\'s slowing you down', 'Answer a few friendly questions in about five minutes. If you can, say roughly how much time or money the problem costs you.'],
                    ['2', 'bg-sage', 'We find the fix', 'AI drafts a plan for your sector and size. Then a real person reads it, checks it and improves it.'],
                    ['3', 'bg-sun', 'See what it\'s worth, then act', 'Your plan shows the simplest fix, what it could save you and first steps for this week. Want a hand putting it in place? Just ask.'],
                ] as [$n, $bg, $heading, $text])
                    <li class="relative rounded-2xl bg-background p-7">
                        <span class="inline-flex w-11 h-11 rounded-full {{ $bg }} font-display text-xl font-semibold items-center justify-center">{{ $n }}</span>
                        <h3 class="mt-5 text-xl font-semibold">{{ $heading }}</h3>
                        <p class="mt-2 text-text-secondary leading-relaxed">{{ $text }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Outcomes, not technology --}}
    <section class="mx-auto max-w-[1100px] px-4 sm:px-6 pt-16 sm:pt-24">
        <div class="max-w-[46rem] mx-auto text-center">
            <p class="text-sm font-semibold text-peach-strong uppercase tracking-wider">Results, not robots</p>
            <h2 class="mt-2 text-3xl sm:text-4xl font-semibold leading-tight">You don't need AI. You need the problem gone.</h2>
            <p class="mt-4 text-lg text-text-secondary leading-relaxed">
                When you have a headache, you don't mind which tablet works. You just want the headache gone. It's the same here. We don't sell chatbots or software. We find where your organisation is losing time, money or focus, and the simplest way to fix it.
            </p>
        </div>
        <div class="mt-10 grid sm:grid-cols-3 gap-6">
            @foreach ([
                ['bg-peach/60', 'Time back', 'Fewer hours lost to rotas, paperwork and chasing people.'],
                ['bg-sage/60', 'Money saved', 'We show what the problem costs you now, and what fixing it is worth.'],
                ['bg-sun/60', 'More focus', 'More of your day for the work that matters, not the admin.'],
            ] as [$bg, $heading, $text])
                <div class="rounded-2xl {{ $bg }} p-7">
                    <h3 class="text-xl font-semibold">{{ $heading }}</h3>
                    <p class="mt-2 text-text-secondary leading-relaxed">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- A real person --}}
    <section class="mx-auto max-w-[1100px] px-4 sm:px-6 py-16 sm:py-24 grid md:grid-cols-[2fr_3fr] gap-10 items-center">
        <x-person name="reviewer-paper" bg="bg-accent-muted" class="w-full max-w-[320px] aspect-square mx-auto" />
        <div>
            <p class="text-sm font-semibold text-peach-strong uppercase tracking-wider">Not just a robot</p>
            <h2 class="mt-2 text-3xl sm:text-4xl font-semibold leading-tight">A real person reads every plan before you do</h2>
            <p class="mt-4 text-lg text-text-secondary leading-relaxed">
                AI is quick at a first draft, but it doesn't know your world like a person does. So every report is checked, edited and approved by someone on our team. If something doesn't fit your situation, we fix it before it reaches you.
            </p>
            <ul class="mt-6 space-y-3">
                @foreach (['Plain English, no tech talk', 'The simplest fix first, nothing over-complicated', 'What fixing it could save you, in hours or pounds', 'Clear first steps you can start this week'] as $point)
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-6 h-6 shrink-0 rounded-full bg-sage flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-sage-strong" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span class="text-text-primary">{{ $point }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- Who it's for --}}
    <section class="bg-surface border-y border-border">
        <div class="mx-auto max-w-[1100px] px-4 sm:px-6 py-16 sm:py-20">
            <p class="text-center text-sm font-semibold text-peach-strong uppercase tracking-wider">Who it's for</p>
            <h2 class="mt-2 text-3xl sm:text-4xl font-semibold text-center">Made for any business or organisation</h2>
            <p class="mt-3 text-center text-text-secondary max-w-[38rem] mx-auto">Whatever you do and however big your team, if something keeps costing you time or money, we can help. These are a few of the problems we hear most.</p>
            <div class="mt-12 grid sm:grid-cols-3 gap-6">
                @foreach ([
                    ['elder-shirt', 'bg-sage', 'Too much admin', 'Paperwork, rotas, reports and typing the same thing twice.'],
                    ['volunteer-hoodie', 'bg-peach', 'Things slipping through', 'Missed enquiries, forgotten follow-ups and lost forms.'],
                    ['owner-explain', 'bg-sun', 'Tools that don\'t talk', 'Spreadsheets, emails and apps that don\'t work together.'],
                ] as [$person, $bg, $heading, $text])
                    <div class="rounded-2xl bg-background p-7 text-center">
                        <x-person :name="$person" :bg="$bg" class="w-28 h-28 mx-auto" />
                        <h3 class="mt-5 text-xl font-semibold">{{ $heading }}</h3>
                        <p class="mt-2 text-text-secondary">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
            <p class="mt-8 text-center text-text-secondary">Something else? Tell us about it. If it costs you time or money, it's worth fixing.</p>
        </div>
    </section>

    {{-- Call to action --}}
    <section class="mx-auto max-w-[1100px] px-4 sm:px-6 py-16 sm:py-20">
        <div class="rounded-3xl bg-gradient-accent px-6 py-12 sm:px-12 sm:py-14 text-center text-white relative overflow-hidden">
            <div class="flex justify-center -space-x-3 mb-6" aria-hidden="true">
                <x-person name="manager-hijab" bg="bg-peach" class="w-14 h-14 ring-4 ring-accent" />
                <x-person name="pastor-coffee" bg="bg-sage" class="w-14 h-14 ring-4 ring-accent" />
                <x-person name="reviewer-paper" bg="bg-sun" class="w-14 h-14 ring-4 ring-accent" />
            </div>
            <h2 class="text-3xl sm:text-4xl font-semibold">Ready to get some time back?</h2>
            <p class="mt-3 text-lg text-white/85">Tell us the problem in about five minutes. We'll come back with a plan to fix it.</p>
            <a href="{{ route('register') }}" class="mt-8 inline-flex items-center justify-center px-7 py-3 rounded-full bg-white text-accent-dark font-semibold hover:bg-accent-muted transition-colors">Get started</a>
        </div>
    </section>

</x-layouts.app>
