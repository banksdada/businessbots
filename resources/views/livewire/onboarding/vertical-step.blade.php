<div>
    <h2 class="text-xl font-semibold mb-1">What best describes you?</h2>
    <p class="text-sm text-text-secondary mb-5">Tap the closest match. It helps us tailor your plan.</p>

    @php
        $common = array_filter($verticals, fn ($v) => empty($v['more']) && $v['slug'] !== 'other');
        $extra = array_filter($verticals, fn ($v) => ! empty($v['more']));
        $other = array_values(array_filter($verticals, fn ($v) => $v['slug'] === 'other'));
        $showExtra = in_array($selectedVertical, array_column($extra, 'slug'), true);
        $colours = ['bg-peach', 'bg-sage', 'bg-sun', 'bg-accent-muted'];
    @endphp

    {{-- Common types first; the rest sit behind "More" to keep the first view short. --}}
    <div x-data="{ more: @js($showExtra) }" class="flex flex-wrap gap-2 mb-2" role="group" aria-label="What best describes you">
        @foreach ([...array_values($common), ...array_values($extra), ...$other] as $i => $vertical)
            @include('livewire.onboarding.partials.vertical-chip', [
                'vertical' => $vertical,
                'colour' => $colours[$i % count($colours)],
                'hidden' => ! empty($vertical['more']),
            ])
            @if ($loop->index === count($common) - 1)
                <button type="button" x-show="!more" x-on:click="more = true"
                    class="inline-flex items-center gap-1 rounded-full px-4 py-2 text-sm font-medium border-2 border-dashed border-border-strong bg-surface text-text-secondary hover:text-text-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-accent">
                    More
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
            @endif
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
