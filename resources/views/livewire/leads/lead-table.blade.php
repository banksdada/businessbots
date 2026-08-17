<div class="mx-auto max-w-[1000px] px-6 py-8">

    <div class="flex items-center mb-5">
        <h1 class="text-lg font-semibold">Leads</h1>
        <div class="ml-auto flex gap-2">
            <select wire:model.live="intentFilter"
                class="px-3 py-1.5 bg-surface border border-border rounded-md text-xs text-text-primary">
                @foreach (\App\Livewire\Leads\LeadTable::INTENT_FILTERS as $filter)
                    <option value="{{ $filter }}">{{ $filter === 'all' ? 'All intents' : ucfirst($filter) }}</option>
                @endforeach
            </select>
            <select wire:model.live="sortBy"
                class="px-3 py-1.5 bg-surface border border-border rounded-md text-xs text-text-primary">
                @foreach (\App\Livewire\Leads\LeadTable::SORT_OPTIONS as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @error('leads')
        <p class="text-xs text-error mb-3">{{ $message }}</p>
    @enderror

    <div class="bg-surface border border-border rounded-xl overflow-hidden">
        <div class="grid grid-cols-[1.3fr_2fr_0.9fr_0.9fr_0.9fr] px-5 py-2.5 border-b border-border text-[10px] font-medium text-text-muted uppercase tracking-wide">
            <div>Name</div><div>Message</div><div>Intent</div><div>AI reply</div><div>Action</div>
        </div>

        @forelse ($leads as $lead)
            @php
                $needsHuman = $lead->intent === 'complaint' || ! $lead->ai_reply_sent;
            @endphp
            <div class="grid grid-cols-[1.3fr_2fr_0.9fr_0.9fr_0.9fr] px-5 py-3.5 items-center border-b border-border last:border-0
                {{ $needsHuman ? 'bg-error/5' : '' }}">

                <div class="text-sm font-medium text-text-primary">{{ $lead->name ?? 'Unknown' }}</div>
                <div class="text-sm text-text-secondary truncate pr-4">{{ $lead->message }}</div>

                <x-badge :variant="match($lead->intent) {
                    'complaint' => 'error',
                    'schedule' => 'success',
                    default => 'accent',
                }">
                    {{ ucfirst($lead->intent) }}
                </x-badge>

                <div class="text-xs {{ $lead->ai_reply_sent ? 'text-success' : 'text-warning' }}">
                    {{ $lead->ai_reply_sent ? '✓ Sent' : 'Needs human' }}
                </div>

                <div>
                    @if ($needsHuman)
                        <a href="tel:{{ $lead->phone }}" class="text-xs font-semibold text-error">Call now</a>
                    @else
                        <button wire:click="markClosed({{ $lead->id }})" class="text-xs font-medium text-accent-light">
                            Mark closed
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="px-5 py-12 text-center text-sm text-text-muted">
                No leads yet — they'll show up here as WhatsApp messages come in.
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $leads->links() }}
    </div>
</div>
