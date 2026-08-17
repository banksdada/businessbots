<?php

namespace App\Services\Channels;

use App\Models\ChannelSetting;
use Illuminate\Support\Facades\Http;

class ChannelTokenRefresher
{
    /**
     * Meta (Instagram/WhatsApp) doesn't use a refresh_token grant like standard
     * OAuth — long-lived tokens are refreshed by re-exchanging the current token
     * itself. Google and LinkedIn use the conventional refresh_token flow.
     * This is the one place that difference is handled.
     */
    public function refresh(ChannelSetting $channel): bool
    {
        try {
            $result = match ($channel->platform) {
                'instagram', 'whatsapp' => $this->refreshMeta($channel),
                'gbp' => $this->refreshStandardOAuth($channel, config('channels.gbp.token_url')),
                'linkedin' => $this->refreshStandardOAuth($channel, config('channels.linkedin.token_url')),
                default => false,
            };

            if (! $result) {
                \Log::warning('[ChannelTokenRefresher] refresh unsupported or failed', [
                    'channel_id' => $channel->id,
                    'platform' => $channel->platform,
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            \Log::error('[ChannelTokenRefresher] refresh threw', [
                'channel_id' => $channel->id,
                'platform' => $channel->platform,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    private function refreshMeta(ChannelSetting $channel): bool
    {
        $config = config("channels.{$channel->platform}");

        $response = Http::get('https://graph.facebook.com/v19.0/oauth/access_token', [
            'grant_type' => 'fb_exchange_token',
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'fb_exchange_token' => $channel->access_token,
        ]);

        if ($response->failed()) {
            $this->markDisconnected($channel);
            return false;
        }

        $data = $response->json();

        $channel->update([
            'access_token' => $data['access_token'],
            'token_expires_at' => isset($data['expires_in']) ? now()->addSeconds((int) $data['expires_in']) : now()->addDays(60),
        ]);

        return true;
    }

    private function refreshStandardOAuth(ChannelSetting $channel, string $tokenUrl): bool
    {
        if (! $channel->refresh_token) {
            $this->markDisconnected($channel);
            return false;
        }

        $config = config("channels.{$channel->platform}");

        $response = Http::asForm()->post($tokenUrl, [
            'grant_type' => 'refresh_token',
            'refresh_token' => $channel->refresh_token,
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
        ]);

        if ($response->failed()) {
            $this->markDisconnected($channel);
            return false;
        }

        $data = $response->json();

        $channel->update([
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'] ?? $channel->refresh_token, // some providers don't rotate it
            'token_expires_at' => isset($data['expires_in']) ? now()->addSeconds((int) $data['expires_in']) : null,
        ]);

        return true;
    }

    /**
     * A refresh failure almost always means the user needs to reconnect —
     * flip is_connected so the settings/onboarding UI shows "Connect" again
     * instead of a channel that silently stopped working.
     */
    private function markDisconnected(ChannelSetting $channel): void
    {
        $channel->update(['is_connected' => false]);
    }
}
