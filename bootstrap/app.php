<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'business.onboarded' => \App\Http\Middleware\EnsureBusinessOnboarded::class,
            'subscribed' => \App\Http\Middleware\EnsureSubscribed::class,
            'meta.signature' => \App\Http\Middleware\VerifyMetaWebhookSignature::class,
        ]);

        // Both webhooks are called by external services (Stripe, Meta) with no
        // session/CSRF token — exempt them, but note both are still protected:
        // Stripe's via Cashier's own signature check, Meta's via meta.signature above.
        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
            'webhooks/whatsapp',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
