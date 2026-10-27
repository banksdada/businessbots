<div>
    <h2 class="text-lg font-semibold mb-1">Connect your channels</h2>
    <p class="text-xs text-text-secondary mb-5">Optional — connect the channels you use now, or add them later.</p>

    <div class="space-y-2.5 mb-2">
        @foreach ($channels as $channel)
            <div class="flex items-center justify-between px-4 py-3 bg-surface-secondary border border-border rounded-lg">
                <span class="text-sm font-medium text-text-primary">{{ $channel['label'] }}</span>

                @if ($connected[$channel['key']])
                    <x-badge variant="success">✓ Connected</x-badge>
                @else
                    <button type="button" wire:click="connect('{{ $channel['key'] }}')"
                        class="px-3 py-1.5 border border-border-strong text-text-primary rounded-md text-xs font-medium">
                        Connect
                    </button>
                @endif
            </div>
        @endforeach
    </div>

    <div class="flex justify-end mt-5">
        <button wire:click="finish" wire:loading.attr="disabled" wire:target="finish"
            class="px-5 py-2.5 bg-gradient-accent text-white rounded-md text-sm font-semibold disabled:opacity-50">
            <span wire:loading.remove wire:target="finish">Finish setup</span>
            <span wire:loading wire:target="finish">Finishing…</span>
        </button>
    </div>
</div>
