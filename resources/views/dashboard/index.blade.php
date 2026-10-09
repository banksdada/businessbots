<x-layouts.app :title="'My requests — ' . config('app.name')">
    <div class="mx-auto max-w-[1100px] px-4 sm:px-6 py-8 sm:py-10">

        @php
            $hour = now()->hour;
            $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
            $firstName = \Illuminate\Support\Str::of(auth()->user()->name)->before(' ');
        @endphp

        <div class="rounded-3xl bg-peach/60 border border-peach px-6 py-7 sm:px-10 sm:py-8 flex items-center gap-6 mb-8 overflow-hidden">
            <div class="flex-1">
                <p class="text-sm font-medium text-text-secondary">{{ $business->name }}</p>
                <h1 class="text-3xl sm:text-4xl font-semibold mt-1">{{ $greeting }}, {{ $firstName }}</h1>
                <p class="mt-2 text-text-secondary max-w-md">What's costing your team time or money right now? Tell us in your own words and we'll find the fix.</p>
                <a href="{{ route('problems.create') }}" class="btn-primary mt-5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                    Tell us a problem
                </a>
            </div>
            <x-person name="manager-hijab" bg="bg-surface" class="hidden sm:block w-40 h-40 shrink-0" />
        </div>

        <h2 class="text-xl font-semibold mb-4">Your requests</h2>

        @if ($problems->isEmpty())
            <div class="card p-8 sm:p-12 text-center">
                <div class="flex justify-center -space-x-3 mb-5" aria-hidden="true">
                    <x-person name="pastor-coffee" bg="bg-sage" class="w-14 h-14 ring-4 ring-surface" />
                    <x-person name="reviewer-paper" bg="bg-sun" class="w-14 h-14 ring-4 ring-surface" />
                </div>
                <h3 class="text-xl font-semibold">No requests yet</h3>
                <p class="mt-2 text-text-secondary max-w-md mx-auto">Staff rotas, paperwork, missed enquiries: whatever is slowing you down, tell us in your own words. You don't need to know what the fix looks like. That's our job. We'll send back a plan, checked by a real person.</p>
                <a href="{{ route('problems.create') }}" class="btn-primary mt-6">Tell us your first problem</a>
            </div>

            <ol class="mt-10 grid sm:grid-cols-3 gap-4" aria-label="How it works">
                @foreach ([
                    ['1', 'bg-peach', 'Tell us the problem', 'A short form, about five minutes.'],
                    ['2', 'bg-sage', 'We find the fix', 'AI drafts it, then a real person checks it.'],
                    ['3', 'bg-sun', 'Get your plan', 'The simplest fix, what it could save you and first steps.'],
                ] as [$n, $bg, $heading, $text])
                    <li class="card p-5">
                        <span class="inline-flex w-9 h-9 rounded-full {{ $bg }} font-display text-lg font-semibold items-center justify-center">{{ $n }}</span>
                        <h3 class="mt-3 font-semibold">{{ $heading }}</h3>
                        <p class="mt-1 text-sm text-text-secondary">{{ $text }}</p>
                    </li>
                @endforeach
            </ol>
        @else
            <ul class="card divide-y divide-border">
                @foreach ($problems as $problem)
                    <li>
                        <a href="{{ route('problems.show', $problem) }}" class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 px-5 py-4 hover:bg-surface-secondary transition-colors">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-text-primary truncate">{{ $problem->title }}</p>
                                <p class="text-sm text-text-muted mt-0.5">Sent {{ $problem->created_at->format('j M Y') }}</p>
                            </div>
                            <x-problem-status :problem="$problem" />
                            <svg class="hidden sm:block w-5 h-5 text-text-faint" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif

        @if (config('features.social'))
            <div class="mt-10">
                @livewire('dashboard.overview', ['business' => $business])
            </div>
        @endif
    </div>
</x-layouts.app>
