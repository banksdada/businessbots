<?php

namespace App\Services\Crm;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CrmCaptureService
{
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
            $response = Http::timeout((int) config('services.crm.timeout_seconds', 10))
                ->withHeaders([
                    'X-AIOS-Timestamp' => $timestamp,
                    'X-AIOS-Signature' => $signature,
                ])
                ->withBody($body, 'application/json')
                ->post($url);

            if (! $response->successful()) {
                Log::warning('CRM capture failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'user_id' => $user->id,
                ]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('CRM capture exception', [
                'message' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return false;
        }
    }
}
