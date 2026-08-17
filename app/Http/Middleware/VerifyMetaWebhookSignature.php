<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyMetaWebhookSignature
{
    /**
     * Registered as 'meta.signature' in bootstrap/app.php.
     * Meta signs every webhook POST with your App Secret — reject anything
     * that doesn't match rather than trusting the payload's own claims.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $signature = $request->header('X-Hub-Signature-256');
        $appSecret = config('services.meta.app_secret');

        if (! $signature || ! $appSecret) {
            \Log::warning('[VerifyMetaWebhookSignature] missing signature or app secret');
            return response('Forbidden', 403);
        }

        $expected = 'sha256=' . hash_hmac('sha256', $request->getContent(), $appSecret);

        if (! hash_equals($expected, $signature)) {
            \Log::warning('[VerifyMetaWebhookSignature] signature mismatch');
            return response('Forbidden', 403);
        }

        return $next($request);
    }
}
