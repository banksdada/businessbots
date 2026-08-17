<?php

namespace App\Livewire\Settings;

use Livewire\Component;

class BillingPanel extends Component
{
    public string $currentTier = '';
    public string $status = 'none'; // none, trialing, active, canceled, past_due
    public ?string $trialEndsAt = null;
    public ?string $renewsAt = null;
    public bool $cancelAtPeriodEnd = false;

    public const TIERS = [
        'starter' => ['name' => 'Starter', 'price' => '£4.99/mo'],
        'professional' => ['name' => 'Professional', 'price' => '£14.99/mo'],
    ];

    public function mount(): void
    {
        $this->refreshStatus();
    }

    public function refreshStatus(): void
    {
        $user = auth()->user();
        $subscription = $user->subscription('default');

        if (! $subscription) {
            $this->status = 'none';
            return;
        }

        $this->status = match (true) {
            $subscription->onTrial() => 'trialing',
            $subscription->canceled() => 'canceled',
            $subscription->pastDue() => 'past_due',
            default => 'active',
        };

        $this->currentTier = $this->tierFromPriceId($subscription->stripe_price);
        $this->trialEndsAt = $subscription->trial_ends_at?->format('j M Y');
        $this->renewsAt = $subscription->ends_at?->format('j M Y');
        $this->cancelAtPeriodEnd = $subscription->onGracePeriod();
    }

    /**
     * Switch plan in place — Stripe prorates automatically. No page reload,
     * no redirect to Checkout, since a payment method is already on file.
     */
    public function switchTo(string $tier): void
    {
        $priceId = config("billing.tiers.{$tier}.stripe_price_id");

        if (! $priceId) {
            $this->addError('plan', 'That plan is not available for self-serve switching.');
            return;
        }

        try {
            auth()->user()->subscription('default')->swap($priceId);
            $this->refreshStatus();
            session()->flash('notice', "Switched to {$this->currentTier}. Changes are prorated on your next invoice.");
        } catch (\Exception $e) {
            \Log::error('[Settings\\BillingPanel] switchTo failed', [
                'user_id' => auth()->id(),
                'tier' => $tier,
                'error' => $e->getMessage(),
            ]);
            $this->addError('plan', 'Could not switch plans. Please try again or contact support.');
        }
    }

    public function cancel(): void
    {
        try {
            auth()->user()->subscription('default')->cancel();
            $this->refreshStatus();
            session()->flash('notice', 'Subscription set to cancel at the end of your billing period.');
        } catch (\Exception $e) {
            \Log::error('[Settings\\BillingPanel] cancel failed', ['error' => $e->getMessage()]);
            $this->addError('plan', 'Could not cancel. Please try again or contact support.');
        }
    }

    public function resume(): void
    {
        try {
            auth()->user()->subscription('default')->resume();
            $this->refreshStatus();
            session()->flash('notice', 'Subscription resumed.');
        } catch (\Exception $e) {
            \Log::error('[Settings\\BillingPanel] resume failed', ['error' => $e->getMessage()]);
            $this->addError('plan', 'Could not resume. Please try again or contact support.');
        }
    }

    private function tierFromPriceId(?string $priceId): string
    {
        foreach (config('billing.tiers') as $key => $tier) {
            if ($tier['stripe_price_id'] === $priceId) {
                return $key;
            }
        }
        return '';
    }

    public function render()
    {
        return view('livewire.settings.billing-panel');
    }
}
