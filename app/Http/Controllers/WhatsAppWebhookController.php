<?php

namespace App\Http\Controllers;

use App\Jobs\WhatsAppHandlerJob;
use App\Models\ChannelSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WhatsAppWebhookController extends Controller
{
    /**
     * Meta's one-time verification handshake when you register the webhook URL
     * in the App Dashboard. Must echo back hub.challenge exactly, as plain text.
     */
    public function verify(Request $request): Response
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token === config('services.meta.webhook_verify_token')) {
            return response($challenge, 200);
        }

        return response('Forbidden', 403);
    }

    /**
     * Inbound messages. Signature is verified by VerifyMetaWebhookSignature
     * middleware before this method ever runs — never trust an unverified payload.
     *
     * Always return 200 quickly, even on internal errors — Meta retries aggressively
     * on non-200s, and heavy processing happens in the queued job, not here.
     */
    public function receive(Request $request): Response
    {
        try {
            $entries = $request->input('entry', []);

            foreach ($entries as $entry) {
                foreach ($entry['changes'] ?? [] as $change) {
                    $this->processChange($change);
                }
            }
        } catch (\Exception $e) {
            \Log::error('[WhatsAppWebhookController] receive failed', ['error' => $e->getMessage()]);
            // Still return 200 — see docblock above.
        }

        return response('EVENT_RECEIVED', 200);
    }

    private function processChange(array $change): void
    {
        $value = $change['value'] ?? [];
        $messages = $value['messages'] ?? [];
        $phoneNumberId = $value['metadata']['phone_number_id'] ?? null;

        if (empty($messages) || ! $phoneNumberId) {
            return; // status callbacks (delivered/read receipts) land here too — nothing to do with those yet
        }

        $businessId = $this->resolveBusinessId($phoneNumberId);

        if (! $businessId) {
            \Log::warning('[WhatsAppWebhookController] no business found for phone_number_id', ['phone_number_id' => $phoneNumberId]);
            return;
        }

        $contacts = collect($value['contacts'] ?? []);

        foreach ($messages as $message) {
            if ($message['type'] !== 'text') {
                continue; // MVP handles text only — media/location messages logged but not processed
            }

            $fromPhone = $message['from'];
            $text = $message['text']['body'] ?? '';
            $senderName = $contacts->firstWhere('wa_id', $fromPhone)['profile']['name'] ?? null;

            WhatsAppHandlerJob::dispatch($businessId, $fromPhone, $text, $senderName);
        }
    }

    /** Maps Meta's phone_number_id back to which business this webhook event belongs to. */
    private function resolveBusinessId(string $phoneNumberId): ?int
    {
        return ChannelSetting::where('platform', 'whatsapp')
            ->where('external_account_id', $phoneNumberId)
            ->value('business_id');
    }
}
