<div>
    <h2 class="text-lg font-semibold mb-1">What type of business do you run?</h2>
    <p class="text-xs text-text-secondary mb-5">We'll tune your AI's tone, topics and replies to fit.</p>

    <div class="grid grid-cols-2 gap-2.5 mb-2">
        @foreach ($verticals as $vertical)
            <button type="button"
                wire:click="selectVertical('{{ $vertical['slug'] }}')"
                class="text-left rounded-xl p-3.5 border-2 transition-colors
                    {{ $selectedVertical === $vertical['slug']
                        ? 'border-accent bg-accent-muted'
                        : 'border-border bg-surface hover:border-border-strong' }}">
                <div class="font-semibold text-sm text-text-primary">{{ $vertical['label'] }}</div>
                <div class="text-xs text-text-secondary mt-0.5">{{ $vertical['description'] }}</div>
            </button>
        @endforeach
    </div>

    @error('selectedVertical')
        <p class="text-xs text-error mb-4">{{ $message }}</p>
    @enderror

    <div class="flex justify-end mt-5">
        <button wire:click="continue" wire:loading.attr="disabled" wire:target="continue"
            class="px-5 py-2.5 bg-gradient-accent text-white rounded-md text-sm font-semibold disabled:opacity-50">
            <span wire:loading.remove wire:target="continue">Continue</span>
            <span wire:loading wire:target="continue">Saving…</span>
        </button>
    </div>
</div>
