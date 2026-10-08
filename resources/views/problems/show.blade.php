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
                <div class="flex items-center gap-3 pb-5 mb-6 border-b border-border">
                    <x-person name="reviewer-paper" bg="bg-sun" class="w-11 h-11" />
                    <p class="text-sm text-text-secondary">Checked and approved by a person on our team<br><span class="text-text-muted">{{ $problem->approved_at->format('j F Y') }}</span></p>
                </div>
                <div class="report">
                    {!! \Illuminate\Support\Str::markdown($problem->final_report, ['html_input' => 'escape', 'allow_unsafe_links' => false]) !!}
                </div>
            </article>
        @else
            @php
                $failed = $problem->status === \App\Models\ProblemRequest::STATUS_FAILED;
                $steps = [
                    ['You sent your question', true],
                    ['AI is writing a first draft', $problem->status !== \App\Models\ProblemRequest::STATUS_PROCESSING],
                    ['A person is checking it', false],
                    ['Your plan is ready', false],
                ];
                $current = collect($steps)->search(fn ($step) => ! $step[1]);
            @endphp
            <div class="card p-6 sm:p-8 mt-6 grid sm:grid-cols-[1fr_auto] gap-8 items-center">
                <div>
                    <h2 class="text-xl font-semibold">{{ $failed ? 'This is taking a little longer than usual' : "We're working on your plan" }}</h2>
                    <p class="mt-2 text-text-secondary">
                        @if ($failed)
                            Something held up the first draft. We've been told and will get it moving again, so there's nothing you need to do.
                        @else
                            We'll email you as soon as it's ready, so there's no need to keep this page open.
                        @endif
                    </p>

                    <ol class="mt-6 space-y-4" aria-label="Progress">
                        @foreach ($steps as $i => [$label, $done])
                            <li class="flex items-center gap-3">
                                @if ($done)
                                    <span class="w-7 h-7 rounded-full bg-sage-strong flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <span class="text-text-primary">{{ $label }}</span>
                                @elseif ($i === $current)
                                    <span class="w-7 h-7 rounded-full border-2 border-accent flex items-center justify-center shrink-0">
                                        <span class="w-2.5 h-2.5 rounded-full bg-accent {{ $failed ? '' : 'animate-pulse' }}"></span>
                                    </span>
                                    <span class="font-semibold text-text-primary">{{ $label }}</span>
                                @else
                                    <span class="w-7 h-7 rounded-full border-2 border-border-strong shrink-0"></span>
                                    <span class="text-text-muted">{{ $label }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </div>
                <x-person name="reviewer-paper" bg="bg-sun" class="hidden sm:block w-44 h-44" />
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
