<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBusinessOnboarded
{
    /**
     * Redirect to onboarding if the user has no active (fully set up) business.
     * Registered as 'business.onboarded' in bootstrap/app.php middleware aliases.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $business = $user->activeBusiness();

        if (! $business) {
            return redirect()->route('onboarding', ['step' => 'vertical']);
        }

        return $next($request);
    }
}
