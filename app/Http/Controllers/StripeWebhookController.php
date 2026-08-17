<?php

namespace App\Http\Controllers;

use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;
use Symfony\Component\HttpFoundation\Response;

class StripeWebhookController extends CashierWebhookController
{
    /**
     * Cashier already handles subscription created/updated/deleted, invoice
     * payment succeeded/failed, and payment method updates out of the box —
     * this override just hooks in anything BusinessBots-specific.
     */
    public function handleCustomerSubscriptionDeleted(array $payload): Response
    {
        $response = parent::handleCustomerSubscriptionDeleted($payload);

        try {
            $user = $this->getUserByStripeId($payload['data']['object']['customer']);
            $business = $user?->activeBusiness();

            if ($business) {
                // Deliberately NOT deactivating the business record — keep their
                // data and config intact in case they resubscribe. Access control
                // (blocking dashboard features) is handled by the subscribed()
                // check in middleware, not by touching is_active here.
                \Log::info('[StripeWebhook] subscription cancelled', ['business_id' => $business->id]);
            }
        } catch (\Exception $e) {
            \Log::error('[StripeWebhook] post-cancellation hook failed', ['error' => $e->getMessage()]);
        }

        return $response;
    }
}
