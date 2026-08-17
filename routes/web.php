<?php

use App\Http\Controllers\MarketingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\WhatsAppWebhookController;
use App\Http\Controllers\ChannelOAuthController;
use App\Livewire\Onboarding\Wizard as OnboardingWizard;
use App\Livewire\Leads\LeadTable;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public / marketing routes — plain Blade, no auth required
|--------------------------------------------------------------------------
*/
Route::get('/', [MarketingController::class, 'home'])->name('home');
Route::get('/pricing', [MarketingController::class, 'pricing'])->name('marketing.pricing');
Route::get('/industries/{vertical?}', [MarketingController::class, 'industries'])->name('marketing.industries');
Route::get('/demo', [MarketingController::class, 'demo'])->name('marketing.demo');

// Public, unauthenticated — the "living proof" metrics page from the reference design
Route::get('/live-proof', [MarketingController::class, 'liveProof'])->name('live-proof');

// Legal / compliance — public, no auth required, linked from footer and signup flow
Route::get('/terms', [LegalController::class, 'terms'])->name('legal.terms');
Route::get('/privacy', [LegalController::class, 'privacy'])->name('legal.privacy');
Route::get('/cookies', [LegalController::class, 'cookies'])->name('legal.cookies');

/*
|--------------------------------------------------------------------------
| Auth routes — Laravel's default controllers (Breeze/Fortify-style)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Onboarding — requires login, but NOT requires an active business yet
| (this is where the business gets created)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/onboarding/{step?}', OnboardingWizard::class)
        ->name('onboarding')
        ->where('step', 'vertical|profile|connect');

    // Channel connect/callback — used by both the onboarding Connect step and
    // "reconnect" actions from Settings once a token expires.
    Route::get('/channels/{platform}/connect', [ChannelOAuthController::class, 'connect'])->name('channels.connect');
    Route::get('/channels/{platform}/callback', [ChannelOAuthController::class, 'callback'])->name('channels.callback');
});

/*
|--------------------------------------------------------------------------
| Billing — Stripe Checkout + billing portal (requires login, not onboarding)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/billing/checkout/{tier}', [BillingController::class, 'checkout'])->name('billing.checkout');
    Route::get('/billing/success', [BillingController::class, 'success'])->name('billing.success');
    Route::get('/billing/portal', [BillingController::class, 'portal'])->name('billing.portal');
});

// Stripe → us. No CSRF, no auth — verified via signature inside Cashier's controller.
// Register STRIPE_WEBHOOK_SECRET in .env and point Stripe's dashboard at this URL.
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])->name('cashier.webhook');

/*
|--------------------------------------------------------------------------
| WhatsApp webhook — Meta verifies via GET once, then POSTs every message.
| No CSRF (external caller), signature verified by middleware instead.
| Register this URL + META_WEBHOOK_VERIFY_TOKEN in the Meta App Dashboard.
|--------------------------------------------------------------------------
*/
Route::get('/webhooks/whatsapp', [WhatsAppWebhookController::class, 'verify'])->name('webhooks.whatsapp.verify');
Route::post('/webhooks/whatsapp', [WhatsAppWebhookController::class, 'receive'])
    ->middleware('meta.signature')
    ->name('webhooks.whatsapp.receive');

/*
|--------------------------------------------------------------------------
| Authenticated app — requires login, onboarded business, AND an active
| subscription or trial. Onboarding itself stays outside this gate so a
| brand-new user can finish setup before ever seeing a paywall.
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'business.onboarded', 'subscribed'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/leads', LeadTable::class)->name('leads.index');
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
});
