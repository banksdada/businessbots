<div>
    <h2 class="text-xl font-semibold mb-1">Tell us about your organisation</h2>
    <p class="text-sm text-text-secondary mb-5">We use this to make your advice specific to you.</p>

    <form wire:submit="continue" class="space-y-4">
        <div>
            <label for="profile-name" class="field-label">Organisation name</label>
            <input type="text" id="profile-name" wire:model="name" placeholder="Bright Care Ltd"
                class="field-input">
            @error('name') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="profile-location" class="field-label">Location</label>
            <input type="text" id="profile-location" wire:model="location" placeholder="Birmingham, UK"
                class="field-input">
            @error('location') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="profile-description" class="field-label">What do you do? (optional)</label>
            <textarea id="profile-description" wire:model="description" rows="3" placeholder="We provide home care visits across the West Midlands…"
                class="field-input"></textarea>
            @error('description') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" wire:loading.attr="disabled" wire:target="continue"
                class="btn-primary">
                <span wire:loading.remove wire:target="continue">Continue</span>
                <span wire:loading wire:target="continue">Saving…</span>
            </button>
        </div>
    </form>
</div>
