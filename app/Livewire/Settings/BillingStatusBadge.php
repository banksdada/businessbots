<?php

namespace App\Livewire\Settings;

use Livewire\Component;

class BillingStatusBadge extends Component
{
    public ?string $state = null; // null = healthy, nothing to show
    public ?string $message = null;
    public string $linkRoute = 'settings';

    public function mount(): void
    {
        $this->evaluateStatus();
    }

    /**
     * Deliberately quiet by default — only renders a badge when something
     * needs the user's attention. An "Active" badge next to Dashboard/Leads
     * every day is nav clutter, not information.
     */
    private function evaluateStatus(): void
    {
        $user = auth()->user();
        $subscription = $user?->subscription('default');

        if (! $subscription) {
            return; // no subscription yet (mid-onboarding, or hasn't picked a plan) — settings page handles this
        }

        if ($subscription->pastDue()) {
            $this->state = 'past_due';
            $this->message = 'Payment failed';
            $this->linkRoute = 'billing.portal';
            return;
        }

        if ($subscription->onGracePeriod()) {
            $endsAt = $subscription->ends_at?->format('j M');
            $this->state = 'cancelling';
            $this->message = "Cancels {$endsAt}";
            $this->linkRoute = 'settings';
            return;
        }

        if ($subscription->onTrial()) {
            $daysLeft = now()->diffInDays($subscription->trial_ends_at, false);
            if ($daysLeft <= 3) {
                $this->state = 'trial_ending';
                $this->message = $daysLeft <= 0 ? 'Trial ends today' : "Trial ends in {$daysLeft}d";
                $this->linkRoute = 'marketing.pricing';
            }
        }
    }

    public function render()
    {
        return view('livewire.settings.billing-status-badge');
    }
}
