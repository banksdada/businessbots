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

                <div class="absolute left-0 top-[10%] card px-4 py-3 max-w-[13rem] -rotate-2">
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
            <h2 class="mt-2 text-3xl sm:text-4xl font-semibold text-center">Five minutes from you. We do the rest.</h2>
            <p class="mt-3 text-center text-text-secondary max-w-[38rem] mx-auto">Picture it. You describe the problem over a cup of tea, then close the page. When your plan is ready, it lands in your inbox.</p>
            <ol class="mt-12 grid md:grid-cols-3 gap-6">
                @foreach ([
                    ['1', 'bg-peach', 'Tell us what\'s slowing you down', 'Answer a few friendly questions in your own words. A rough guess at the hours or money it costs you helps a lot.'],
                    ['2', 'bg-sage', 'We find the fix', 'AI writes a first draft for your situation. Then a real person reads it, checks it and makes it better.'],
                    ['3', 'bg-sun', 'See what it\'s worth, then act', 'You get the simplest fix, what it could save you, and steps you can start this week. Want us to do it for you? Just ask.'],
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
                Think about the last time you had a headache. You didn't care which tablet worked. You just wanted the headache gone. Your business problems are the same. So we don't sell chatbots or software. We find where you're losing time, money or focus, and the simplest way to stop it.
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

    {{-- AIOS: the system behind the fix --}}
    <section id="aios" class="mx-auto max-w-[1100px] px-4 sm:px-6 pb-16 sm:pb-24 scroll-mt-20">
        <div class="overflow-hidden rounded-[2rem] bg-text-primary text-white">
            <div class="grid gap-10 p-7 sm:p-10 lg:grid-cols-[1.05fr_0.95fr] lg:items-center lg:p-12">
                <div>
                    <p class="text-sm font-semibold text-accent2 uppercase tracking-wider">AIOS — the system behind the fix</p>
                    <h2 class="mt-3 text-3xl sm:text-4xl font-semibold leading-tight">The fix doesn't have to become another job.</h2>
                    <p class="mt-4 text-lg text-white/75 leading-relaxed">
                        AIOS is the AI Operating System we set up around your business. It keeps the repeat work moving — enquiries answered, follow-ups sent, content posted, numbers reported — without adding another tool for you to manage.
                    </p>
                    <ul class="mt-6 space-y-3 text-white/85">
                        @foreach ([
                            'Replies and follow-ups that don\'t wait for a quiet day.',
                            'A weekly report that arrives already written.',
                            'A real person checks the important bits.',
                        ] as $point)
                            <li class="flex items-start gap-3">
                                <span class="mt-1.5 w-1.5 h-1.5 shrink-0 rounded-full bg-accent2"></span>
                                <span>{{ $point }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('contact', ['about' => 'AIOS']) }}" class="inline-flex items-center justify-center px-6 py-3 rounded-full bg-white text-text-primary font-semibold hover:bg-accent-muted transition-colors">
                            Ask about AIOS
                        </a>
                        <a href="#how-it-works" class="inline-flex items-center justify-center px-6 py-3 rounded-full border border-white/25 text-white font-semibold hover:bg-white/10 transition-colors">
                            See how it works
                        </a>
                    </div>
                    <p class="mt-4 text-sm text-white/60">No jargon. No new tools for you to manage.</p>
                </div>

                <div class="rounded-2xl border border-white/20 bg-white/10 p-5 sm:p-6">
                    <p class="text-xs font-semibold uppercase tracking-wider text-white/55">Example workflow</p>

                    <svg viewBox="0 0 360 220" class="mt-3 h-auto w-full" role="img" aria-labelledby="aios-map-title aios-map-desc">
                        <title id="aios-map-title">How AIOS connects the repeat work</title>
                        <desc id="aios-map-desc">AIOS sits at the centre of enquiries, follow-ups, content and reports.</desc>
                        <defs>
                            <linearGradient id="aios-core" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0" stop-color="#ff4da6"/>
                                <stop offset="1" stop-color="#8e4dff"/>
                            </linearGradient>
                        </defs>

                        <g fill="none" stroke="rgba(255,255,255,0.18)" stroke-width="1.5" stroke-dasharray="4 6">
                            <path d="M180 110 C 150 95, 130 70, 110 47"/>
                            <path d="M180 110 C 210 95, 230 70, 250 47"/>
                            <path d="M180 110 C 150 125, 130 150, 110 173"/>
                            <path d="M180 110 C 210 125, 230 150, 250 173"/>
                        </g>

                        <circle cx="180" cy="110" r="54" fill="none" stroke="rgba(255,255,255,0.18)"/>
                        <circle cx="180" cy="110" r="44" fill="url(#aios-core)"/>
                        <text x="180" y="115" text-anchor="middle" fill="#ffffff" font-size="15" font-weight="700" font-family="Inter, sans-serif">AIOS</text>

                        <g font-family="Inter, sans-serif" font-size="11" font-weight="600">
                            <g>
                                <rect x="20" y="30" width="110" height="34" rx="17" fill="rgba(255,255,255,0.08)" stroke="rgba(255,255,255,0.2)"/>
                                <circle cx="38" cy="47" r="4" fill="#ff4da6"/>
                                <text x="50" y="51" fill="rgba(255,255,255,0.82)">Enquiries</text>
                            </g>
                            <g>
                                <rect x="230" y="30" width="110" height="34" rx="17" fill="rgba(255,255,255,0.08)" stroke="rgba(255,255,255,0.2)"/>
                                <circle cx="248" cy="47" r="4" fill="#dcebdf"/>
                                <text x="260" y="51" fill="rgba(255,255,255,0.82)">Follow-ups</text>
                            </g>
                            <g>
                                <rect x="20" y="156" width="110" height="34" rx="17" fill="rgba(255,255,255,0.08)" stroke="rgba(255,255,255,0.2)"/>
                                <circle cx="38" cy="173" r="4" fill="#fbe8b0"/>
                                <text x="50" y="177" fill="rgba(255,255,255,0.82)">Content</text>
                            </g>
                            <g>
                                <rect x="230" y="156" width="110" height="34" rx="17" fill="rgba(255,255,255,0.08)" stroke="rgba(255,255,255,0.2)"/>
                                <circle cx="248" cy="173" r="4" fill="#8e4dff"/>
                                <text x="260" y="177" fill="rgba(255,255,255,0.82)">Reports</text>
                            </g>
                        </g>
                    </svg>

                    <div class="mt-6 flex items-center justify-between gap-4">
                        <p class="font-display text-xl font-semibold">A Tuesday with AIOS</p>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-success/15 px-2.5 py-1 text-xs font-semibold text-white/80">
                            <span class="w-1.5 h-1.5 rounded-full bg-success"></span>
                            Running
                        </span>
                    </div>

                    <ol class="mt-4 space-y-5">
                        @foreach ([
                            ['08:12', 'New enquiry from the website', 'Reply drafted in your voice, ready for your OK.'],
                            ['11:30', 'Quote still unanswered', 'A gentle follow-up goes out, so you don\'t have to remember.'],
                            ['17:00', 'End of the day', 'The week\'s numbers land in your inbox, already written.'],
                        ] as [$time, $title, $detail])
                            <li class="grid grid-cols-[3.25rem_1fr] gap-3">
                                <span class="pt-0.5 text-xs font-medium tabular-nums text-white/45">{{ $time }}</span>
                                <div class="border-l border-white/20 pl-4">
                                    <p class="font-semibold">{{ $title }}</p>
                                    <p class="mt-1 text-sm leading-relaxed text-white/65">{{ $detail }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>

                    <p class="mt-6 border-t border-white/20 pt-4 text-xs leading-relaxed text-white/50">
                        Example workflow. Yours is built around your actual work — and you approve the voice before anything goes out.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- A real person --}}
    <section class="mx-auto max-w-[1100px] px-4 sm:px-6 py-16 sm:py-24 grid md:grid-cols-[2fr_3fr] gap-10 items-center">
        <x-person name="reviewer-paper" bg="bg-accent-muted" class="w-full max-w-[320px] aspect-square mx-auto" />
        <div>
            <p class="text-sm font-semibold text-peach-strong uppercase tracking-wider">The honest part</p>
            <h2 class="mt-2 text-3xl sm:text-4xl font-semibold leading-tight">AI gets things wrong. That's why a person checks every plan.</h2>
            <p class="mt-4 text-lg text-text-secondary leading-relaxed">
                We'll be straight with you. AI is fast at a first draft, but it doesn't know your world. So someone on our team reads every plan, edits it and approves it. If something doesn't fit your situation, we fix it before you ever see it.
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

    {{-- Questions people ask: raise each doubt and answer it plainly --}}
    <section class="mx-auto max-w-[760px] px-4 sm:px-6 pb-16 sm:pb-24">
        <p class="text-center text-sm font-semibold text-peach-strong uppercase tracking-wider">Fair questions</p>
        <h2 class="mt-2 text-3xl sm:text-4xl font-semibold text-center">What you might be wondering</h2>
        <div class="mt-10 space-y-3">
            @foreach ([
                ['Do I need to understand AI or technology?', 'No. Explain the problem the way you would to a friend. Working out the technology is our job, not yours.'],
                ['What if my problem seems too small?', 'If it costs you time or money every week, it isn\'t small. Small problems that keep coming back are often the quickest to fix.'],
                ['Will I be pushed into buying something?', 'No. Your plan is yours to keep. Follow it yourself, or ask us to help put the fix in place. It\'s your choice.'],
                ['How much will a fix cost?', 'We agree a price with you before any work starts, and you\'ll see exactly what\'s included. No surprises.'],
                ['How long does it take?', 'Telling us takes about five minutes. Then you can close the page. We email you as soon as your plan has been checked and is ready.'],
            ] as [$question, $answer])
                <details class="card group">
                    <summary class="cursor-pointer list-none px-6 py-4 font-semibold flex items-center justify-between gap-4">
                        {{ $question }}
                        <svg class="w-5 h-5 shrink-0 text-text-faint transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <p class="px-6 pb-5 text-text-secondary leading-relaxed">{{ $answer }}</p>
                </details>
            @endforeach
        </div>
    </section>

    {{-- Who it's for --}}
    <section class="bg-surface border-y border-border">
        <div class="mx-auto max-w-[1100px] px-4 sm:px-6 py-16 sm:py-20">
            <p class="text-center text-sm font-semibold text-peach-strong uppercase tracking-wider">Who it's for</p>
            <h2 class="mt-2 text-3xl sm:text-4xl font-semibold text-center">Made for any business or organisation</h2>
            <p class="mt-3 text-center text-text-secondary max-w-[38rem] mx-auto">Whatever you do and however big your team, if something keeps costing you time or money, we can help. Here are a few common ones.</p>
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
            <h2 class="text-3xl sm:text-4xl font-semibold">What's one problem you'd love to be rid of?</h2>
            <p class="mt-3 text-lg text-white/85">Tell us about it now. It takes about five minutes, and a real person will check your plan before it reaches you.</p>
            <a href="{{ route('register') }}" class="mt-8 inline-flex items-center justify-center px-7 py-3 rounded-full bg-white text-accent-dark font-semibold hover:bg-accent-muted transition-colors">Tell us your problem</a>
        </div>
    </section>

</x-layouts.app>
