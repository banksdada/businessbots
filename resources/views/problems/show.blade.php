<x-layouts.app :title="$problem->title . ' — ' . config('app.name')">
    <div class="mx-auto max-w-[760px] px-4 sm:px-6 py-8 sm:py-10">
        <a href="{{ route('dashboard') }}" class="text-sm font-medium text-text-secondary hover:text-text-primary">&larr; My requests</a>

        <div class="mt-4 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <h1 class="text-2xl sm:text-3xl font-bold">{{ $problem->title }}</h1>
            <x-problem-status :problem="$problem" class="self-start" />
        </div>
        <p class="mt-2 text-sm text-text-muted">Sent {{ $problem->created_at->format('j F Y') }}</p>

        @if ($problem->isReady())
            <article class="card p-6 sm:p-8 mt-6">
                <p class="text-sm text-text-muted mb-4">Reviewed and sent {{ $problem->approved_at->format('j F Y') }}</p>
                <div class="report">
                    {!! \Illuminate\Support\Str::markdown($problem->final_report, ['html_input' => 'escape', 'allow_unsafe_links' => false]) !!}
                </div>
            </article>
        @else
            <div class="card p-6 sm:p-8 mt-6 flex gap-4 items-start">
                <div class="w-10 h-10 shrink-0 rounded-full bg-accent-muted flex items-center justify-center">
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h2 class="font-semibold">We're preparing your report</h2>
                    <p class="mt-1 text-text-secondary">Our AI writes a first draft, then a person checks and improves it. We'll email you as soon as it's ready, so there's no need to keep this page open.</p>
                </div>
            </div>
        @endif

        <details class="card mt-6 group">
            <summary class="cursor-pointer list-none px-6 py-4 font-semibold flex items-center justify-between">
                What you told us
                <svg class="w-5 h-5 text-text-faint transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <dl class="px-6 pb-6 space-y-4 text-sm">
                <div><dt class="font-medium text-text-muted">The problem</dt><dd class="mt-1 whitespace-pre-line">{{ $problem->description }}</dd></div>
                @if ($problem->already_tried)
                    <div><dt class="font-medium text-text-muted">Already tried</dt><dd class="mt-1 whitespace-pre-line">{{ $problem->already_tried }}</dd></div>
                @endif
                @if ($problem->current_tools)
                    <div><dt class="font-medium text-text-muted">Tools used now</dt><dd class="mt-1">{{ $problem->current_tools }}</dd></div>
                @endif
                <div class="grid sm:grid-cols-3 gap-4">
                    <div><dt class="font-medium text-text-muted">Staff</dt><dd class="mt-1">{{ $problem->staff_count ?? 'Not given' }}</dd></div>
                    <div><dt class="font-medium text-text-muted">Urgency</dt><dd class="mt-1">{{ \App\Models\ProblemRequest::URGENCIES[$problem->urgency] ?? $problem->urgency }}</dd></div>
                    <div><dt class="font-medium text-text-muted">Wanted</dt><dd class="mt-1">{{ \App\Models\ProblemRequest::HELP_OPTIONS[$problem->help_wanted] ?? $problem->help_wanted }}</dd></div>
                </div>
            </dl>
        </details>
    </div>
</x-layouts.app>
