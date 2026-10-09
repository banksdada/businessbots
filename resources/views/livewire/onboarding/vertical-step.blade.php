<div>
    <h2 class="text-xl font-semibold mb-1">What best describes you?</h2>
    <p class="text-sm text-text-secondary mb-5">Tap the closest match. It helps us tailor your plan.</p>

    {{-- Short, tappable chips keep all the choices on one phone screen. --}}
    <div class="flex flex-wrap gap-2 mb-2" role="group" aria-label="What best describes you">
        @foreach ($verticals as $vertical)
            @php($selected = $selectedVertical === $vertical['slug'])
            <button type="button"
                wire:click="selectVertical('{{ $vertical['slug'] }}')"
                aria-pressed="{{ $selected ? 'true' : 'false' }}"
                title="{{ $vertical['description'] }}"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-1.5 rounded-full px-4 py-2 text-sm font-medium border-2 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-accent
                    {{ $selected
                        ? 'border-accent bg-accent-muted text-text-primary'
                        : 'border-border bg-surface text-text-secondary hover:border-border-strong hover:text-text-primary' }}">
                @if ($selected)
                    <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                @endif
                {{ $vertical['label'] }}
            </button>
        @endforeach
    </div>

    @error('selectedVertical')
        <p class="field-error">{{ $message }}</p>
    @enderror

    {{-- Tapping a type moves on by itself; Continue only shows when coming back with one already picked. --}}
    @if ($selectedVertical)
    <div class="flex justify-end mt-5">
        <button wire:click="continue" wire:loading.attr="disabled" wire:target="continue"
            class="btn-primary">
            <span wire:loading.remove wire:target="continue">Continue</span>
            <span wire:loading wire:target="continue">Saving…</span>
        </button>
    </div>
    @endif
</div>
