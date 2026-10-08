<div class="mx-auto max-w-[720px] px-4 sm:px-6 py-8 sm:py-10">
    <a href="{{ route('dashboard') }}" class="text-sm font-medium text-text-secondary hover:text-text-primary">&larr; My requests</a>

    <h1 class="text-2xl sm:text-3xl font-bold mt-4">Ask for advice</h1>
    <p class="mt-2 text-text-secondary">Tell us what's getting in the way. The more detail you give, the more useful your report will be. A person reviews every report before you see it.</p>

    <div class="mt-6 flex items-center gap-4 rounded-2xl bg-sun/60 p-4 sm:p-5">
        <x-person name="owner-explain" bg="bg-sun-strong/20" class="w-14 h-14 shrink-0" />
        <p class="text-sm text-text-secondary"><span class="font-semibold text-text-primary">Tip:</span> write it the way you'd explain it to a friend over a cup of tea. Plain words are perfect.</p>
    </div>

    <form wire:submit="submit" class="card p-5 sm:p-8 mt-6 space-y-6" novalidate>
        <div>
            <label for="title" class="field-label">Give it a short title</label>
            <input id="title" type="text" wire:model.blur="title" class="field-input" maxlength="120"
                placeholder="e.g. Staff rotas take too long every week">
            @error('title') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description" class="field-label">Describe the problem</label>
            <textarea id="description" wire:model.blur="description" rows="6" class="field-input"
                aria-describedby="description-help"
                placeholder="What happens, how often, and who it affects."></textarea>
            <p id="description-help" class="field-help">Please don't include names or personal details of the people you support.</p>
            @error('description') <p class="field-error">{{ $message }}</p> @enderror
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

        <fieldset>
            <legend class="field-label">How urgent is it?</legend>
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

        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 pt-2 border-t border-border">
            <a href="{{ route('dashboard') }}" class="btn-secondary mt-4 sm:mt-6">Cancel</a>
            <button type="submit" class="btn-primary mt-4 sm:mt-6" wire:loading.attr="disabled" wire:target="submit">
                <span wire:loading.remove wire:target="submit">Send request</span>
                <span wire:loading wire:target="submit">Sending…</span>
            </button>
        </div>
    </form>
</div>
