<?php

namespace App\Http\Controllers;

use App\Jobs\WhatsAppHandlerJob;
use App\Models\ChannelSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Handle Meta webhook verification.
     *
     * Meta sends:
     * hub.mode
     * hub.verify_token
     * hub.challenge
     *
     * Depending on PHP/Laravel handling, dotted query-string keys may
     * also appear as underscore versions, so this supports both.
     */
    public function verify(Request $request): Response
    {
        $mode = (string) (
            $request->query('hub_mode')
            ?? $request->query('hub.mode')
            ?? ''
        );

        $token = (string) (
            $request->query('hub_verify_token')
            ?? $request->query('hub.verify_token')
            ?? ''
        );

        $challenge = (string) (
            $request->query('hub_challenge')
            ?? $request->query('hub.challenge')
            ?? ''
        );

        $expectedToken = (string) config(
            'services.meta.webhook_verify_token',
            ''
        );

        Log::info('Meta WhatsApp webhook verification attempt', [
            'mode' => $mode,
            'token_received' => $token !== '',
            'challenge_received' => $challenge !== '',
            'verify_token_configured' => $expectedToken !== '',
        ]);

        if (
            $mode === 'subscribe'
            && $token !== ''
            && $expectedToken !== ''
            && hash_equals($expectedToken, $token)
        ) {
            Log::info('Meta WhatsApp webhook verified successfully');

            return response(
                $challenge,
                200,
                [
                    'Content-Type' => 'text/plain',
                ]
            );
        }

        Log::warning('Meta WhatsApp webhook verification failed', [
            'mode' => $mode,
            'token_received' => $token !== '',
            'verify_token_configured' => $expectedToken !== '',
        ]);

        return response(
            'Forbidden',
            403,
            [
                'Content-Type' => 'text/plain',
            ]
        );
    }

    /**
     * Receive webhook events from Meta.
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
        } catch (\Throwable $e) {
            Log::error('WhatsApp webhook processing failed', [
                'error' => $e->getMessage(),
            ]);
        }

        /*
         * Always acknowledge Meta quickly so it does not keep retrying
         * a webhook because of an internal BusinessBots error.
         */
        return response(
            'EVENT_RECEIVED',
            200,
            [
                'Content-Type' => 'text/plain',
            ]
        );
    }

    /**
     * Process an individual webhook change.
     */
    private function processChange(array $change): void
    {
        $value = $change['value'] ?? [];

        $messages = $value['messages'] ?? [];

        $phoneNumberId =
            $value['metadata']['phone_number_id'] ?? null;

        /*
         * Meta also sends delivery/read/status events.
         * BusinessBots only processes actual incoming messages here.
         */
        if (empty($messages) || ! $phoneNumberId) {
            return;
        }

        $businessId = $this->resolveBusinessId(
            (string) $phoneNumberId
        );

        if (! $businessId) {
            Log::warning(
                'No BusinessBots business matched WhatsApp Phone Number ID',
                [
                    'phone_number_id' => $phoneNumberId,
                ]
            );

            return;
        }

        $contacts = collect(
            $value['contacts'] ?? []
        );

        foreach ($messages as $message) {
            $messageType =
                $message['type'] ?? null;

            /*
             * Current MVP handles text messages.
             * Images/audio/documents can be added later.
             */
            if ($messageType !== 'text') {
                Log::info(
                    'Unsupported WhatsApp message type received',
                    [
                        'type' => $messageType,
                        'business_id' => $businessId,
                    ]
                );

                continue;
            }

            $fromPhone =
                $message['from'] ?? null;

            $text =
                $message['text']['body'] ?? '';

            if (
                ! $fromPhone
                || trim((string) $text) === ''
            ) {
                continue;
            }

            $contact = $contacts->firstWhere(
                'wa_id',
                $fromPhone
            );

            $senderName =
                $contact['profile']['name'] ?? null;

            WhatsAppHandlerJob::dispatch(
                $businessId,
                $fromPhone,
                $text,
                $senderName
            );
        }
    }

    /**
     * Find the BusinessBots business associated with Meta's
     * WhatsApp Phone Number ID.
     */
    private function resolveBusinessId(
        string $phoneNumberId
    ): ?int {
        $businessId = ChannelSetting::query()
            ->where('platform', 'whatsapp')
            ->where(
                'external_account_id',
                $phoneNumberId
            )
            ->value('business_id');

        return $businessId
            ? (int) $businessId
            : null;
    }
}
