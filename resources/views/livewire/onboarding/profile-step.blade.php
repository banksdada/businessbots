<div>
    <h2 class="text-xl font-semibold mb-1">Tell us about your organisation</h2>
    <p class="text-sm text-text-secondary mb-5">We use this to make your advice specific to you.</p>

    <form wire:submit="continue" class="space-y-4">
        <div>
            <label for="profile-name" class="field-label">Organisation name</label>
            <input type="text" id="profile-name" wire:model="name" placeholder="e.g. Bright Co Ltd"
                class="field-input">
            @error('name') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="profile-location" class="field-label">Location <span class="font-normal text-text-muted">(optional)</span></label>
            <input type="text" id="profile-location" wire:model="location" placeholder="e.g. Birmingham, UK"
                class="field-input">
            @error('location') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="profile-description" class="field-label">Tell us a bit about what you do <span class="font-normal text-text-muted">(optional)</span></label>
            <textarea id="profile-description" wire:model="description" rows="3" placeholder="e.g. We sell handmade furniture online and from our shop."
                class="field-input"></textarea>
            @error('description') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('onboarding', ['step' => 'vertical']) }}" wire:navigate class="text-sm font-medium text-text-secondary hover:text-text-primary">&larr; Back</a>
            <button type="submit" wire:loading.attr="disabled" wire:target="continue"
                class="btn-primary">
                <span wire:loading.remove wire:target="continue">Continue</span>
                <span wire:loading wire:target="continue">Saving…</span>
            </button>
        </div>
    </form>
</div>
