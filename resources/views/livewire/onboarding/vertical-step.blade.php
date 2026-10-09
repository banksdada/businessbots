<div>
    <h2 class="text-xl font-semibold mb-1">What kind of organisation are you?</h2>
    <p class="text-sm text-text-secondary mb-5">Pick the closest one so we can tailor your advice. If none fit, choose Other.</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 mb-2">
        @foreach ($verticals as $vertical)
            <button type="button"
                wire:click="selectVertical('{{ $vertical['slug'] }}')"
                aria-pressed="{{ $selectedVertical === $vertical['slug'] ? 'true' : 'false' }}"
                class="text-left rounded-xl p-3.5 border-2 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-accent
                    {{ $selectedVertical === $vertical['slug']
                        ? 'border-accent bg-accent-muted'
                        : 'border-border bg-surface hover:border-border-strong' }}">
                <div class="font-semibold text-sm text-text-primary">{{ $vertical['label'] }}</div>
                <div class="text-sm text-text-secondary mt-0.5">{{ $vertical['description'] }}</div>
            </button>
        @endforeach
    </div>

    @error('selectedVertical')
        <p class="field-error">{{ $message }}</p>
    @enderror

    <div class="flex justify-end mt-5">
        <button wire:click="continue" wire:loading.attr="disabled" wire:target="continue"
            class="btn-primary">
            <span wire:loading.remove wire:target="continue">Continue</span>
            <span wire:loading wire:target="continue">Saving…</span>
        </button>
    </div>
</div>
