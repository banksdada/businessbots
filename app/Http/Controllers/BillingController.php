<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class BillingController extends Controller
{
    /**
     * Start a Stripe Checkout session for the chosen tier.
     * Enterprise has no self-serve price — routes to the demo/contact page instead.
     */
    public function checkout(Request $request, string $tier): RedirectResponse|Response
    {
        $config = config("billing.tiers.{$tier}");

        if (! $config || empty($config['stripe_price_id'])) {
            return redirect()->route('marketing.demo')
                ->with('notice', 'Talk to us to set up Enterprise billing.');
        }

        try {
            $user = $request->user();
            $trialDays = $user->hasEverSubscribed() ? 0 : config('billing.trial_days');

            $checkout = $user
                ->newSubscription('default', $config['stripe_price_id'])
                ->trialDays($trialDays)
                ->checkout([
                    'success_url' => route('billing.success') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('marketing.pricing'),
                ]);

            return $checkout;
        } catch (\Exception $e) {
            \Log::error('[BillingController] checkout failed', [
                'user_id' => $request->user()->id,
                'tier' => $tier,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('marketing.pricing')
                ->with('error', 'Could not start checkout. Please try again or contact support.');
        }
    }

    public function success(Request $request): RedirectResponse
    {
        // Stripe's webhook (not this route) is the source of truth for activation —
        // this just gets the user somewhere sensible after a successful redirect.
        return redirect()->route('dashboard')->with('notice', 'Subscription active — welcome aboard.');
    }

    /**
     * Redirect to Stripe's hosted billing portal — plan changes, cancellation,
     * invoice history, and payment method updates all happen there, not in our UI.
     */
    public function portal(Request $request): RedirectResponse|Response
    {
        try {
            return $request->user()->redirectToBillingPortal(route('settings'));
        } catch (\Exception $e) {
            \Log::error('[BillingController] portal redirect failed', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('settings')
                ->with('error', 'Could not open billing portal. Please try again.');
        }
    }
}
