<div class="bg-surface border border-border rounded-xl p-6">
    <h2 class="text-sm font-semibold mb-1">Business details</h2>
    <p class="text-xs text-text-secondary mb-5">This shapes the tone your AI uses in replies and posts.</p>

    @if (session('notice'))
        <div class="mb-4 px-3 py-2 bg-success-muted border border-success/20 rounded-md text-xs text-success">
            {{ session('notice') }}
        </div>
    @endif

    <form wire:submit="save" class="space-y-4">
        <div>
            <label class="block text-xs font-medium text-text-secondary mb-1.5">Business name</label>
            <input type="text" wire:model="name"
                class="w-full px-3 py-2 bg-surface-secondary border border-border rounded-md text-sm text-text-primary focus:outline-none focus:ring-1 focus:ring-accent">
            @error('name') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-medium text-text-secondary mb-1.5">Location</label>
            <input type="text" wire:model="location"
                class="w-full px-3 py-2 bg-surface-secondary border border-border rounded-md text-sm text-text-primary focus:outline-none focus:ring-1 focus:ring-accent">
            @error('location') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-medium text-text-secondary mb-1.5">Description</label>
            <textarea wire:model="description" rows="3"
                class="w-full px-3 py-2 bg-surface-secondary border border-border rounded-md text-sm text-text-primary focus:outline-none focus:ring-1 focus:ring-accent"></textarea>
            @error('description') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-medium text-text-secondary mb-1.5">Industry</label>
            <div class="flex items-center justify-between px-3 py-2 bg-surface-secondary border border-border rounded-md">
                <span class="text-sm text-text-primary">{{ ucfirst(str_replace('_', ' ', $verticalLabel)) }}</span>
                <span class="text-[10px] text-text-faint">Contact support to change</span>
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" wire:loading.attr="disabled" wire:target="save"
                class="px-4 py-2 bg-gradient-accent text-white rounded-md text-xs font-semibold disabled:opacity-50">
                <span wire:loading.remove wire:target="save">Save changes</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>
        </div>
    </form>
</div>
