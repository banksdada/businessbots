<?php

namespace App\Http\Controllers;

use App\Models\ChannelSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ChannelOAuthController extends Controller
{
    private const PLATFORMS = ['instagram', 'whatsapp', 'linkedin', 'gbp'];

    /**
     * Redirect to the platform's authorize screen. A signed random `state`
     * is stashed in session and re-checked on callback — standard CSRF
     * protection for OAuth redirects, not optional.
     */
    public function connect(Request $request, string $platform): RedirectResponse
    {
        $config = $this->configFor($platform);

        $state = Str::random(40);
        session(["oauth_state.{$platform}" => $state]);

        $query = http_build_query([
            'client_id' => $config['client_id'],
            'redirect_uri' => $config['redirect_uri'],
            'scope' => $config['scope'],
            'response_type' => 'code',
            'state' => $state,
        ]);

        return redirect($config['authorize_url'] . '?' . $query);
    }

    /**
     * Exchange the authorization code for an access token, resolve the
     * external account identity, and store it — encrypted, per the
     * ChannelSetting model's casts — against the user's active business.
     */
    public function callback(Request $request, string $platform): RedirectResponse
    {
        $config = $this->configFor($platform);

        $expectedState = session("oauth_state.{$platform}");
        session()->forget("oauth_state.{$platform}");

        if (! $expectedState || $request->query('state') !== $expectedState) {
            return $this->backToOnboarding()->with('error', 'Connection failed — please try again.');
        }

        if ($request->query('error')) {
            // User declined on the platform's consent screen — not a bug, just a "no thanks"
            return $this->backToOnboarding()->with('notice', ucfirst($platform) . ' connection was cancelled.');
        }

        try {
            $tokenResponse = Http::asForm()->post($config['token_url'], [
                'grant_type' => 'authorization_code',
                'code' => $request->query('code'),
                'client_id' => $config['client_id'],
                'client_secret' => $config['client_secret'],
                'redirect_uri' => $config['redirect_uri'],
            ]);

            if ($tokenResponse->failed()) {
                throw new \RuntimeException("Token exchange failed: {$tokenResponse->body()}");
            }

            $tokenData = $tokenResponse->json();
            $accessToken = $tokenData['access_token'] ?? null;

            if (! $accessToken) {
                throw new \RuntimeException('No access_token in provider response.');
            }

            $identity = $this->resolveExternalIdentity($platform, $accessToken);

            if ($platform === 'gbp' && empty($identity['id'])) {
                // Don't save a channel connection that can't actually post —
                // that failure should surface now, at connect time, not three
                // days later as a mysterious "post failed" in the queue logs.
                return $this->backToOnboarding()->with(
                    'error',
                    'Could not find a Google Business Profile location on this account. Make sure you\'re signed in with the Google account that manages your listing.'
                );
            }

            $business = $this->currentBusiness($request);

            ChannelSetting::updateOrCreate(
                ['business_id' => $business->id, 'platform' => $platform],
                [
                    'access_token' => $accessToken,
                    'refresh_token' => $tokenData['refresh_token'] ?? null,
                    'token_expires_at' => isset($tokenData['expires_in'])
                        ? now()->addSeconds((int) $tokenData['expires_in'])
                        : null,
                    'external_account_id' => $identity['id'] ?? null,
                    'external_account_name' => $identity['name'] ?? null,
                    'is_connected' => true,
                ]
            );

            return $this->backToOnboarding()->with('notice', $config['label'] . ' connected.');
        } catch (\Exception $e) {
            \Log::error('[ChannelOAuthController] callback failed', [
                'platform' => $platform,
                'error' => $e->getMessage(),
            ]);

            return $this->backToOnboarding()->with('error', 'Could not connect ' . $config['label'] . '. Please try again.');
        }
    }

    /**
     * Each platform reports account identity differently — this is the one place
     * that knowledge lives, so callback() itself stays platform-agnostic.
     */
    private function resolveExternalIdentity(string $platform, string $accessToken): array
    {
        return match ($platform) {
            'instagram' => $this->fetchJson('https://graph.facebook.com/v19.0/me/accounts', $accessToken, ['id', 'name']),
            'whatsapp' => $this->fetchJson('https://graph.facebook.com/v19.0/me', $accessToken, ['id', 'name']),
            'linkedin' => $this->fetchJson('https://api.linkedin.com/v2/me', $accessToken, ['id', 'localizedFirstName']),
            'gbp' => $this->resolveGbpLocation($accessToken),
            default => [],
        };
    }

    /**
     * GBP posts are addressed by "accounts/{accountId}/locations/{locationId}",
     * not a plain user ID — a generic userinfo call (what the other platforms
     * use) doesn't give us anything postable. Two real API calls: list the
     * user's Business Profile accounts, then list that account's locations.
     *
     * LIMITATION: picks the first account and first location only. A business
     * with multiple locations gets whichever one Google returns first — there's
     * no location-picker UI. Fine for single-location businesses (the MVP
     * target per project-overview.md), a real gap for multi-location ones.
     */
    private function resolveGbpLocation(string $accessToken): array
    {
        try {
            $accountsResponse = Http::withToken($accessToken)
                ->get('https://mybusinessaccountmanagement.googleapis.com/v1/accounts');

            $accountName = $accountsResponse->json('accounts.0.name'); // "accounts/{accountId}"

            if (! $accountName) {
                \Log::warning('[ChannelOAuthController] no GBP account found for this Google login');
                return [];
            }

            $locationsResponse = Http::withToken($accessToken)
                ->get("https://mybusinessbusinessinformation.googleapis.com/v1/{$accountName}/locations", [
                    'readMask' => 'name,title',
                ]);

            $locationName = $locationsResponse->json('locations.0.name'); // "accounts/{id}/locations/{id}"
            $locationTitle = $locationsResponse->json('locations.0.title');

            if (! $locationName) {
                \Log::warning('[ChannelOAuthController] GBP account has no locations', ['account' => $accountName]);
                return [];
            }

            return ['id' => $locationName, 'name' => $locationTitle ?? 'Google Business Profile'];
        } catch (\Exception $e) {
            \Log::error('[ChannelOAuthController] GBP location resolution failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function fetchJson(string $url, string $token, array $keys): array
    {
        try {
            $response = Http::withToken($token)->get($url)->json();
            return ['id' => $response[$keys[0]] ?? null, 'name' => $response[$keys[1]] ?? null];
        } catch (\Exception $e) {
            \Log::warning('[ChannelOAuthController] identity fetch failed', ['url' => $url, 'error' => $e->getMessage()]);
            return [];
        }
    }

    private function configFor(string $platform): array
    {
        if (! in_array($platform, self::PLATFORMS, true)) {
            abort(404);
        }

        $config = config("channels.{$platform}");

        if (empty($config['client_id'])) {
            abort(500, ucfirst($platform) . ' is not configured — missing client ID in .env.');
        }

        return $config;
    }

    private function currentBusiness(Request $request)
    {
        return $request->user()->businesses()->where('is_active', false)->latest()->first()
            ?? $request->user()->activeBusiness()
            ?? abort(404, 'No business found for this account.');
    }

    private function backToOnboarding(): RedirectResponse
    {
        return redirect()->route('onboarding', ['step' => 'connect']);
    }
}
