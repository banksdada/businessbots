<div class="bg-surface border border-border rounded-xl p-6">
    <h2 class="text-sm font-semibold mb-1">Billing</h2>
    <p class="text-xs text-text-secondary mb-5">Manage your plan, payment method, and invoices.</p>

    @if (session('notice'))
        <div class="mb-4 px-3 py-2 bg-success-muted border border-success/20 rounded-md text-xs text-success">
            {{ session('notice') }}
        </div>
    @endif
    @error('plan')
        <div class="mb-4 px-3 py-2 bg-error/10 border border-error/20 rounded-md text-xs text-error">
            {{ $message }}
        </div>
    @enderror

    {{-- Current status --}}
    <div class="flex items-center justify-between px-4 py-3 bg-surface-secondary border border-border rounded-lg mb-5">
        <div>
            <div class="text-sm font-medium text-text-primary">
                {{ $currentTier ? \App\Livewire\Settings\BillingPanel::TIERS[$currentTier]['name'] : 'No active plan' }}
            </div>
            <div class="text-xs text-text-muted mt-0.5">
                @switch($status)
                    @case('trialing') Trial ends {{ $trialEndsAt }} @break
                    @case('active') @if($cancelAtPeriodEnd) Cancels {{ $renewsAt }} @else Renews monthly @endif @break
                    @case('canceled') Cancelled — access until {{ $renewsAt }} @break
                    @case('past_due') Payment failed — update your card @break
                    @default No subscription yet
                @endswitch
            </div>
        </div>

        @if ($status === 'past_due')
            <x-badge variant="error">Action needed</x-badge>
        @elseif ($status === 'trialing')
            <x-badge variant="accent">Trial</x-badge>
        @elseif ($status === 'active' && ! $cancelAtPeriodEnd)
            <x-badge variant="success">Active</x-badge>
        @endif
    </div>

    {{-- Editable plan switcher --}}
    @if ($status !== 'none')
        <div class="mb-5">
            <div class="text-xs font-medium text-text-secondary mb-2">Change plan</div>
            <div class="grid grid-cols-2 gap-2.5">
                @foreach (\App\Livewire\Settings\BillingPanel::TIERS as $key => $tier)
                    <button type="button" wire:click="switchTo('{{ $key }}')"
                        wire:loading.attr="disabled" wire:target="switchTo('{{ $key }}')"
                        @disabled($key === $currentTier)
                        class="text-left rounded-lg p-3 border-2 transition-colors disabled:opacity-50
                            {{ $key === $currentTier ? 'border-accent bg-accent-muted' : 'border-border bg-surface hover:border-border-strong' }}">
                        <div class="font-semibold text-sm text-text-primary">{{ $tier['name'] }}</div>
                        <div class="text-xs text-text-secondary mt-0.5">{{ $tier['price'] }}</div>
                        @if ($key === $currentTier)
                            <div class="text-[10px] text-accent-light mt-1">Current plan</div>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Actions --}}
    <div class="flex items-center gap-3 pt-4 border-t border-border">
        <a href="{{ route('billing.portal') }}"
            class="px-4 py-2 border border-border-strong text-text-primary rounded-md text-xs font-medium">
            Manage payment method &amp; invoices
        </a>

        @if ($status === 'active' && ! $cancelAtPeriodEnd)
            <button type="button" wire:click="cancel" wire:loading.attr="disabled" wire:target="cancel"
                class="px-4 py-2 text-xs font-medium text-error">
                Cancel subscription
            </button>
        @elseif ($cancelAtPeriodEnd)
            <button type="button" wire:click="resume" wire:loading.attr="disabled" wire:target="resume"
                class="px-4 py-2 text-xs font-medium text-accent-light">
                Resume subscription
            </button>
        @elseif ($status === 'none')
            <a href="{{ route('marketing.pricing') }}" class="px-4 py-2 bg-gradient-accent text-white rounded-md text-xs font-semibold">
                Choose a plan
            </a>
        @endif
    </div>
</div>
