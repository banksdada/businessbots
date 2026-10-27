<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Coolify's proxy (Traefik) ends HTTPS and forwards plain HTTP. Trusting
        // its X-Forwarded-* headers makes Laravel build https:// links for CSS,
        // JS and Livewire; without it browsers block them as "mixed content"
        // and pages load unstyled.
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'business.onboarded' => \App\Http\Middleware\EnsureBusinessOnboarded::class,
            'subscribed' => \App\Http\Middleware\EnsureSubscribed::class,
        ]);

        // Stripe's webhook is called by an external service with no session or
        // CSRF token. It is still protected by Cashier's signature check.
        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
