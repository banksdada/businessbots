@php
    $failed = $problem->status === \App\Models\ProblemRequest::STATUS_FAILED;
    $steps = [
        ['You told us the problem', true],
        // A failed draft is still stuck on this step, not past it.
        ['AI is writing a first draft', $problem->status === \App\Models\ProblemRequest::STATUS_PENDING_REVIEW],
        ['A person is checking it', false],
        ['Your plan is ready', false],
    ];
    $current = collect($steps)->search(fn ($step) => ! $step[1]);
@endphp

<div wire:poll.15s class="mt-6 space-y-6">
    {{-- Most clients watch the screen, so lead with "you can go". --}}
    <div class="flex items-start gap-4 rounded-2xl bg-sage p-5 sm:p-6">
        <span class="w-11 h-11 rounded-full bg-surface flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-sage-strong" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </span>
        <div>
            <p class="font-semibold text-text-primary">You don't need to wait here</p>
            <p class="mt-1 text-sm text-text-secondary">
                We'll email <span class="font-medium text-text-primary">{{ $email }}</span> as soon as your plan is ready.
                A real person reads every plan before it's sent, so it won't arrive straight away. Feel free to close this page.
            </p>
        </div>
    </div>

    <div class="card p-6 sm:p-8 grid sm:grid-cols-[1fr_auto] gap-8 items-center">
        <div>
            <h2 class="text-xl font-semibold">{{ $failed ? 'This is taking a little longer than usual' : "We're working on your plan" }}</h2>
            @if ($failed)
                <p class="mt-2 text-text-secondary">Something held up the first draft. We've been told and will get it moving again, so there's nothing you need to do.</p>
            @endif

            <ol class="mt-6 space-y-4" aria-label="Progress">
                @foreach ($steps as $i => [$label, $done])
                    <li class="flex items-center gap-3">
                        @if ($done)
                            <span class="w-7 h-7 rounded-full bg-sage-strong flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <span class="text-text-primary">{{ $label }}</span>
                        @elseif ($i === $current)
                            <span class="relative w-7 h-7 rounded-full border-2 border-accent flex items-center justify-center shrink-0">
                                @unless ($failed)
                                    <span class="absolute inset-0 rounded-full border-2 border-accent animate-ping opacity-40"></span>
                                @endunless
                                <span class="w-2.5 h-2.5 rounded-full bg-accent"></span>
                            </span>
                            <span class="font-semibold text-text-primary">{{ $label }}</span>
                            @unless ($failed)
                                <span class="typing-dots" aria-hidden="true"><span></span><span></span><span></span></span>
                            @endunless
                        @else
                            <span class="w-7 h-7 rounded-full border-2 border-border-strong shrink-0"></span>
                            <span class="text-text-muted">{{ $label }}</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>
        <x-person name="reviewer-paper" bg="bg-sun" class="hidden sm:block w-44 h-44 {{ $failed ? '' : 'animate-float' }}" />
    </div>

    {{-- Rotating tips give the client something useful to read while they wait. --}}
    <div wire:ignore
        x-data="{ tips: @js($tips), i: 0 }"
        x-init="setInterval(() => i = (i + 1) % tips.length, 7000)"
        class="card p-6 sm:p-7 flex items-start gap-4">
        <x-person name="owner-explain" bg="bg-peach" class="w-14 h-14 shrink-0" />
        <div class="min-w-0 flex-1">
            <p class="text-xs font-semibold uppercase tracking-wider text-peach-strong">While you wait</p>
            <div class="relative mt-2 min-h-[6rem] sm:min-h-[4.5rem]">
                <template x-for="(tip, n) in tips" :key="n">
                    <p x-show="i === n"
                        x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="absolute inset-0 text-text-primary" x-text="tip"></p>
                </template>
                <noscript><p class="text-text-primary">{{ $tips[0] }}</p></noscript>
            </div>
            <div class="mt-4 flex gap-1.5" aria-hidden="true">
                <template x-for="(tip, n) in tips" :key="n">
                    <button type="button" @click="i = n"
                        class="h-1.5 rounded-full transition-all"
                        :class="i === n ? 'w-6 bg-peach-strong' : 'w-1.5 bg-border-strong'"></button>
                </template>
            </div>
        </div>
    </div>
</div>
