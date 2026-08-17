<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscribed
{
    /**
     * Registered as 'subscribed' in bootstrap/app.php.
     * Cashier's subscribed()/onTrial() cover both "paying" and "still in trial" —
     * only a lapsed trial with no payment method gets redirected to pricing.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user->subscribed('default') || $user->onTrial('default')) {
            return $next($request);
        }

        return redirect()->route('marketing.pricing')
            ->with('notice', 'Your trial has ended — pick a plan to keep using BusinessBots.');
    }
}
