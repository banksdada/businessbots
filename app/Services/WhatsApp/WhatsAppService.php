<?php

namespace App\Services\WhatsApp;

use App\Models\ChannelSetting;
use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    /**
     * Send a text message via the business's connected WhatsApp number.
     * Falls back to the platform-level token/number if the business hasn't
     * connected their own yet (useful during onboarding/trial).
     */
    public function sendMessage(int $businessId, string $toPhone, string $text): array
    {
        $channel = ChannelSetting::where('business_id', $businessId)
            ->where('platform', 'whatsapp')
            ->where('is_connected', true)
            ->first();

        $token = $channel?->access_token ?? config('services.meta.whatsapp_token');
        $phoneNumberId = $channel?->external_account_id ?? config('services.meta.whatsapp_phone_number_id');

        if (! $token || ! $phoneNumberId) {
            throw new \RuntimeException('No WhatsApp channel connected for this business.');
        }

        $response = Http::withToken($token)
            ->post("https://graph.facebook.com/v19.0/{$phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $toPhone,
                'type' => 'text',
                'text' => ['body' => $text],
            ]);

        if ($response->failed()) {
            throw new \RuntimeException("WhatsApp send failed: {$response->body()}");
        }

        return $response->json();
    }
}
