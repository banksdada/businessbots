<div class="mx-auto max-w-[720px] px-4 sm:px-6 py-8 sm:py-10">
    <a href="{{ route('dashboard') }}" class="text-sm font-medium text-text-secondary hover:text-text-primary">&larr; My requests</a>

    <h1 class="text-2xl sm:text-3xl font-bold mt-4">Tell us what's slowing you down</h1>
    <p class="mt-2 text-text-secondary">Focus on the problem, not the technology. You don't need to know what the fix looks like. That's our job. The more detail you give, the more useful your plan will be, and a person checks every plan before you see it.</p>

    <div class="mt-6 flex items-center gap-4 rounded-2xl bg-sun/60 p-4 sm:p-5">
        <x-person name="owner-explain" bg="bg-sun-strong/20" class="w-14 h-14 shrink-0" />
        <p class="text-sm text-text-secondary"><span class="font-semibold text-text-primary">Tip:</span> write it the way you'd explain it to a friend over a cup of tea. Rough guesses are fine. Only the first two questions are required.</p>
    </div>

    <form wire:submit="submit" class="mt-6 space-y-6" novalidate>
        {{-- 1. Diagnose: what is going wrong --}}
        <section class="card p-5 sm:p-8 space-y-6" aria-labelledby="step-problem">
            <div class="flex items-center gap-3">
                <span class="inline-flex w-9 h-9 rounded-full bg-peach font-display text-lg font-semibold items-center justify-center shrink-0">1</span>
                <div>
                    <h2 id="step-problem" class="text-lg font-semibold">The problem</h2>
                    <p class="text-sm text-text-muted">What's going wrong today?</p>
                </div>
            </div>

            <div>
                <label for="title" class="field-label">What's the problem, in a few words?</label>
                <input id="title" type="text" wire:model.blur="title" class="field-input" maxlength="120"
                    placeholder="e.g. Staff rotas take too long every week">
                @error('title') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="field-label">What happens now?</label>
                <textarea id="description" wire:model.blur="description" rows="5" class="field-input"
                    aria-describedby="description-help"
                    placeholder="Walk us through it: what happens, how often, and who it affects."></textarea>
                <p id="description-help" class="field-help">Please don't include names or personal details of your customers, clients or staff.</p>
                @error('description') <p class="field-error">{{ $message }}</p> @enderror
            </div>
        </section>

        {{-- 2. Value: what it costs them, in their own numbers --}}
        <section class="card p-5 sm:p-8 space-y-6" aria-labelledby="step-cost">
            <div class="flex items-center gap-3">
                <span class="inline-flex w-9 h-9 rounded-full bg-sage font-display text-lg font-semibold items-center justify-center shrink-0">2</span>
                <div>
                    <h2 id="step-cost" class="text-lg font-semibold">What it's costing you</h2>
                    <p class="text-sm text-text-muted">This is how we show what fixing it is worth.</p>
                </div>
            </div>

            <fieldset>
                <legend class="field-label">What is it costing you? <span class="font-normal text-text-muted">(tick any)</span></legend>
                <div class="grid sm:grid-cols-2 gap-2.5">
                    @foreach ($impactOptions as $value => $label)
                        <label class="flex items-center gap-2.5 rounded-lg border px-3.5 py-3 cursor-pointer transition-colors {{ in_array($value, $impacts, true) ? 'border-accent bg-accent-muted' : 'border-border-strong hover:bg-surface-secondary' }}">
                            <input type="checkbox" wire:model.live="impacts" value="{{ $value }}" class="accent-[var(--color-accent)]">
                            <span class="text-sm font-medium">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('impacts.*') <p class="field-error">{{ $message }}</p> @enderror
            </fieldset>

            <div class="grid sm:grid-cols-2 gap-6">
                <div>
                    <label for="hours_per_week" class="field-label">Hours a week it takes <span class="font-normal text-text-muted">(optional)</span></label>
                    <input id="hours_per_week" type="number" min="0.5" step="0.5" inputmode="decimal" wire:model.live.debounce.400ms="hours_per_week" class="field-input" placeholder="e.g. 8"
                        aria-describedby="hours-help">
                    <p id="hours-help" class="field-help">Add up everyone's time. A guess is fine.</p>
                    @error('hours_per_week') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="hourly_cost" class="field-label">Cost of an hour of their time, in £ <span class="font-normal text-text-muted">(optional)</span></label>
                    <input id="hourly_cost" type="number" min="1" step="1" inputmode="numeric" wire:model.live.debounce.400ms="hourly_cost" class="field-input" placeholder="e.g. 15"
                        aria-describedby="cost-help">
                    <p id="cost-help" class="field-help">Roughly what you pay the people doing it.</p>
                    @error('hourly_cost') <p class="field-error">{{ $message }}</p> @enderror
                </div>
            </div>

            @if ($yearlyCost)
                <div class="flex items-start gap-3 rounded-xl bg-sage p-4" aria-live="polite">
                    <span class="mt-0.5 w-6 h-6 shrink-0 rounded-full bg-sage-strong flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <p class="text-sm text-text-secondary">By your numbers, this problem takes <span class="font-semibold text-text-primary">{{ $yearlyCost }}</span>. That's what a good fix can win back.</p>
                </div>
            @endif
        </section>

        {{-- Sections 3 and 4 are optional, so they stay folded away to keep the form short on phones. --}}
        <div x-data="{ more: @js($errors->hasAny(['desired_outcome', 'already_tried', 'current_tools', 'staff_count', 'urgency', 'help_wanted'])) }" class="space-y-6">
            <button type="button" x-on:click="more = !more" x-bind:aria-expanded="more.toString()" aria-controls="more-detail"
                class="w-full card p-5 sm:px-8 flex items-center justify-between gap-4 text-left hover:bg-surface-secondary transition-colors">
                <span>
                    <span class="block font-semibold text-text-primary">Add more detail <span class="font-normal text-text-muted">(optional)</span></span>
                    <span class="block mt-0.5 text-sm text-text-secondary">What "fixed" looks like, what you've tried, how soon you need it and the help you'd like. It all makes your plan more useful.</span>
                </span>
                <svg class="w-5 h-5 shrink-0 text-text-faint transition-transform" x-bind:class="more && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div id="more-detail" x-show="more" x-cloak class="space-y-6">
                {{-- 3. Solve: what "fixed" looks like to them --}}
                <section class="card p-5 sm:p-8 space-y-6" aria-labelledby="step-fixed">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex w-9 h-9 rounded-full bg-sun font-display text-lg font-semibold items-center justify-center shrink-0">3</span>
                        <div>
                            <h2 id="step-fixed" class="text-lg font-semibold">What "fixed" looks like</h2>
                            <p class="text-sm text-text-muted">So we aim for the result you actually want.</p>
                        </div>
                    </div>

                    <div>
                        <label for="desired_outcome" class="field-label">If this were fixed, what would be different? <span class="font-normal text-text-muted">(optional)</span></label>
                        <textarea id="desired_outcome" wire:model.blur="desired_outcome" rows="3" class="field-input"
                            placeholder="e.g. Rotas done in an hour, and staff get them a week earlier."></textarea>
                        @error('desired_outcome') <p class="field-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="already_tried" class="field-label">What have you already tried? <span class="font-normal text-text-muted">(optional)</span></label>
                        <textarea id="already_tried" wire:model.blur="already_tried" rows="3" class="field-input"
                            placeholder="e.g. We tried a spreadsheet but people stopped updating it."></textarea>
                        @error('already_tried') <p class="field-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label for="current_tools" class="field-label">Tools or software you use now <span class="font-normal text-text-muted">(optional)</span></label>
                            <input id="current_tools" type="text" wire:model.blur="current_tools" class="field-input"
                                placeholder="e.g. Excel, WhatsApp, Google Drive">
                            @error('current_tools') <p class="field-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="staff_count" class="field-label">Number of staff <span class="font-normal text-text-muted">(optional)</span></label>
                            <input id="staff_count" type="number" min="1" inputmode="numeric" wire:model.blur="staff_count" class="field-input" placeholder="e.g. 25">
                            @error('staff_count') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                {{-- 4. How we help --}}
                <section class="card p-5 sm:p-8 space-y-6" aria-labelledby="step-help">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex w-9 h-9 rounded-full bg-accent-muted font-display text-lg font-semibold items-center justify-center shrink-0">4</span>
                        <div>
                            <h2 id="step-help" class="text-lg font-semibold">How we can help</h2>
                            <p class="text-sm text-text-muted">You can change your mind later.</p>
                        </div>
                    </div>

                    <fieldset>
                        <legend class="field-label">How soon do you need it fixed?</legend>
                        <div class="grid sm:grid-cols-3 gap-2.5">
                            @foreach ($urgencies as $value => $label)
                                <label class="flex items-center gap-2.5 rounded-lg border px-3.5 py-3 cursor-pointer transition-colors {{ $urgency === $value ? 'border-accent bg-accent-muted' : 'border-border-strong hover:bg-surface-secondary' }}">
                                    <input type="radio" wire:model.live="urgency" value="{{ $value }}" class="accent-[var(--color-accent)]">
                                    <span class="text-sm font-medium">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('urgency') <p class="field-error">{{ $message }}</p> @enderror
                    </fieldset>

                    <fieldset>
                        <legend class="field-label">What would you like?</legend>
                        <div class="grid sm:grid-cols-2 gap-2.5">
                            @foreach ($helpOptions as $value => $label)
                                <label class="flex items-center gap-2.5 rounded-lg border px-3.5 py-3 cursor-pointer transition-colors {{ $help_wanted === $value ? 'border-accent bg-accent-muted' : 'border-border-strong hover:bg-surface-secondary' }}">
                                    <input type="radio" wire:model.live="help_wanted" value="{{ $value }}" class="accent-[var(--color-accent)]">
                                    <span class="text-sm font-medium">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('help_wanted') <p class="field-error">{{ $message }}</p> @enderror
                    </fieldset>
                </section>
            </div>
        </div>

        <section class="card p-5 sm:p-8">
            {{-- Errors show beside the questions further up; say so here, where the client pressed the button. --}}
            @if ($errors->any())
                <p role="alert" class="mb-4 rounded-lg bg-error-muted px-4 py-3 text-sm text-text-primary">A couple of answers need a little more. Please check the questions marked in red above.</p>
            @endif

            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
                <a href="{{ route('dashboard') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:target="submit">
                    <span wire:loading.remove wire:target="submit">Find me a fix</span>
                    <span wire:loading wire:target="submit">Sending…</span>
                </button>
            </div>
        </section>
    </form>
</div>
