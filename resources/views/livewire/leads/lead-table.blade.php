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
            <div>Name</div><div>Message</div><div>Intent</div><div>Status</div><div>Action</div>
        </div>

        @forelse ($leads as $lead)
            @php
                $needsHuman = $lead->status !== 'closed';
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

                <x-badge :variant="match($lead->status) {
                    'closed' => 'success',
                    'followup' => 'warning',
                    default => 'accent',
                }">
                    {{ ucfirst($lead->status) }}
                </x-badge>

                <div>
                    @if ($needsHuman)
                        <div class="flex items-center gap-2">
                            <a href="tel:{{ $lead->phone }}" class="text-xs font-semibold text-error">Call now</a>
                            <button wire:click="markClosed({{ $lead->id }})" class="text-xs font-medium text-accent-light">
                                Mark closed
                            </button>
                        </div>
                    @else
                        <span class="text-xs text-text-muted">Closed</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="px-5 py-12 text-center text-sm text-text-muted">
                No leads yet — they'll show up here as enquiries come in.
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $leads->links() }}
    </div>
</div>
