<div>
    <h2 class="text-lg font-semibold mb-1">Tell us about your business</h2>
    <p class="text-xs text-text-secondary mb-5">This shapes the tone and details your AI uses in replies and posts.</p>

    <form wire:submit="continue" class="space-y-4">
        <div>
            <label class="block text-xs font-medium text-text-secondary mb-1.5">Business name</label>
            <input type="text" wire:model="name" placeholder="Bright Care Ltd"
                class="w-full px-3 py-2 bg-surface-secondary border border-border rounded-md text-sm text-text-primary placeholder:text-text-faint focus:outline-none focus:ring-1 focus:ring-accent">
            @error('name') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-medium text-text-secondary mb-1.5">Location</label>
            <input type="text" wire:model="location" placeholder="Birmingham, UK"
                class="w-full px-3 py-2 bg-surface-secondary border border-border rounded-md text-sm text-text-primary placeholder:text-text-faint focus:outline-none focus:ring-1 focus:ring-accent">
            @error('location') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-medium text-text-secondary mb-1.5">What do you do? (optional)</label>
            <textarea wire:model="description" rows="3" placeholder="We provide home care visits across the West Midlands…"
                class="w-full px-3 py-2 bg-surface-secondary border border-border rounded-md text-sm text-text-primary placeholder:text-text-faint focus:outline-none focus:ring-1 focus:ring-accent"></textarea>
            @error('description') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" wire:loading.attr="disabled" wire:target="continue"
                class="px-5 py-2.5 bg-gradient-accent text-white rounded-md text-sm font-semibold disabled:opacity-50">
                <span wire:loading.remove wire:target="continue">Continue</span>
                <span wire:loading wire:target="continue">Saving…</span>
            </button>
        </div>
    </form>
</div>
