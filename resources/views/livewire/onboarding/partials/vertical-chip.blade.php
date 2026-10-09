@php($selected = $selectedVertical === $vertical['slug'])
<button type="button"
    wire:click="selectVertical('{{ $vertical['slug'] }}')"
    aria-pressed="{{ $selected ? 'true' : 'false' }}"
    title="{{ $vertical['description'] }}"
    wire:loading.attr="disabled"
    @if ($hidden) x-show="more" x-cloak @endif
    class="inline-flex items-center gap-1.5 rounded-full px-4 py-2 text-sm font-medium text-text-primary border-2 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-accent hover:brightness-95 {{ $colour }}
        {{ $selected ? 'border-accent' : 'border-transparent' }}">
    @if ($selected)
        <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    @endif
    {{ $vertical['label'] }}
</button>
