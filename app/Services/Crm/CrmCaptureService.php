<?php

namespace App\Services\Crm;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Push a verified sign-up into the AIOS CRM.
 *
 * Called from AuthController::verify() once the magic link has been clicked —
 * that is the only moment we know the email is real. The AIOS accepts a
 * signed POST at /webhook/crm and appends the contact to the Clients sheet.
 *
 * The signature is HMAC-SHA256 over "<timestamp>.<body>" using the shared
 * CRM_WEBHOOK_SECRET, sent as X-AIOS-Timestamp / X-AIOS-Signature. This is the
 * same scheme the AIOS uses for its other webhooks (scripts/dashboard/
 * pyrunner_events.py), so the receiving side is already tested.
 *
 * Fail-safe by design: a CRM outage must never stop a user logging in. Every
 * failure is logged and swallowed; the caller never gets an exception.
 */
class CrmCaptureService
{
    /**
     * Notify the AIOS of a verified user. Never throws.
     *
     * @return bool true if the AIOS accepted the contact.
     */
    public function capture(User $user): bool
    {
        $url = config('services.crm.webhook_url');
        $secret = config('services.crm.webhook_secret');

        if (empty($url) || empty($secret)) {
            Log::warning('CRM capture skipped: url or secret not configured', [
                'has_url' => ! empty($url),
                'has_secret' => ! empty($secret),
            ]);

            return false;
        }

        $body = json_encode([
            'name' => (string) $user->name,
            'email' => strtolower((string) $user->email),
            'stage' => 'Lead',
        ], JSON_UNESCAPED_SLASHES);

        $timestamp = (string) time();
        $signature = 'sha256=' . hash_hmac('sha256', $timestamp . '.' . $body, $secret);

        try {
            $response = Http::timeout(config('services.crm.timeout_seconds', 10))
                ->withHeaders([
                    'X-AIOS-Timestamp' => $timestamp,
                    'X-AIOS-Signature' => $signature,
                    'User-Agent' => config('services.crm.user_agent', 'Sorted/1.0'),
                ])
                ->withBody($body, 'application/json')
                ->post($url);

            if ($response->failed()) {
                Log::warning('CRM capture rejected by AIOS', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            // Network error, DNS failure, timeout — log it, but let the login
            // carry on. The user is already authenticated by this point.
            Log::warning('CRM capture failed to reach the AIOS', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
