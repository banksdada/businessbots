<x-layouts.app :title="'My requests — ' . config('app.name')">
    <div class="mx-auto max-w-[1100px] px-4 sm:px-6 py-8 sm:py-10">

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
            <div>
                <p class="text-sm font-medium text-text-muted">{{ $business->name }}</p>
                <h1 class="text-2xl sm:text-3xl font-bold mt-1">Your advice requests</h1>
            </div>
            <a href="{{ route('problems.create') }}" class="btn-primary self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                Ask for advice
            </a>
        </div>

        @if ($problems->isEmpty())
            <div class="card p-8 sm:p-12 text-center">
                <div class="mx-auto w-12 h-12 rounded-full bg-accent-muted flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8M8 14h5m-9 6l2.5-3H19a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v14z"/></svg>
                </div>
                <h2 class="text-lg font-semibold">No requests yet</h2>
                <p class="mt-2 text-text-secondary max-w-md mx-auto">Tell us about a problem in your organisation, such as staff rotas, paperwork or getting more enquiries. We'll send you a reviewed report with practical next steps.</p>
                <a href="{{ route('problems.create') }}" class="btn-primary mt-6">Ask your first question</a>
            </div>

            <ol class="mt-10 grid sm:grid-cols-3 gap-4" aria-label="How it works">
                @foreach ([
                    ['1', 'Describe the problem', 'A short form, about five minutes.'],
                    ['2', 'We prepare your report', 'AI drafts it, then a person checks it.'],
                    ['3', 'Read your advice', 'We email you when it\'s ready.'],
                ] as [$n, $heading, $text])
                    <li class="card p-5">
                        <span class="inline-flex w-7 h-7 rounded-full bg-accent-muted text-accent text-sm font-bold items-center justify-center">{{ $n }}</span>
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
