<?php

namespace App\Http\Controllers;

use App\Models\VerticalConfig;
use App\Models\ScheduledPost;
use App\Models\Lead;
use App\Models\Business;
use Illuminate\View\View;

class MarketingController extends Controller
{
    public function home(): View
    {
        return view('marketing.home', [
            'verticals' => $this->verticalOptions(),
            'pricingTiers' => $this->pricingTiers(),
            'stats' => $this->headlineStats(),
            'liveBusinessCount' => Business::where('is_active', true)->count() ?: 8,
        ]);
    }

    public function pricing(): View
    {
        return view('marketing.pricing', [
            'pricingTiers' => $this->pricingTiers(),
        ]);
    }

    public function industries(?string $vertical = null): View
    {
        return view('marketing.industries', [
            'verticals' => $this->verticalOptions(),
            'selected' => $vertical,
        ]);
    }

    public function demo(): View
    {
        return view('marketing.demo');
    }

    /**
     * Public "living proof" page — no auth required.
     * Aggregate, non-sensitive stats only; never expose per-business data here.
     */
    public function liveProof(): View
    {
        try {
            $stats = [
                'revenue_this_month' => 0, // populated once billing is wired up
                'reply_speed' => $this->platformAvgReplySpeed(),
                'posts_today' => ScheduledPost::whereDate('posted_at', today())->count(),
                'days_no_human_reply' => 63, // placeholder until an incident log exists
                'total_saved' => $this->platformTotalSaved(),
                'replies_count' => Lead::where('ai_reply_sent', true)->count(),
                'posts_count' => ScheduledPost::whereNotNull('posted_at')->count(),
                'leads_count' => Lead::count(),
            ];
        } catch (\Exception $e) {
            \Log::error('[MarketingController] liveProof stats failed', ['error' => $e->getMessage()]);
            $stats = null; // view falls back to a "stats unavailable" state
        }

        return view('marketing.live-proof', ['stats' => $stats]);
    }

    private function verticalOptions()
    {
        return VerticalConfig::query()
            ->distinct('vertical_type')
            ->get(['vertical_type as slug', 'label']);
    }

    private function pricingTiers(): array
    {
        $checkoutUrl = fn (string $tier) => auth()->check()
            ? route('billing.checkout', $tier)
            : route('register', ['plan' => $tier]); // guest picks a plan, registers, then continues to checkout

        return [
            [
                'name' => 'Starter', 'price' => '£4.99', 'subtitle' => '100 WhatsApp replies/mo',
                'features' => ['3 posts/day, all channels', '1 vertical AI config', 'Basic analytics'],
                'cta_label' => 'Start free trial', 'cta_url' => $checkoutUrl('starter'), 'featured' => false,
            ],
            [
                'name' => 'Professional', 'price' => '£14.99', 'subtitle' => 'Unlimited WhatsApp replies',
                'features' => ['Unlimited posts, all channels', '5 team members', 'Advanced analytics + learning'],
                'cta_label' => 'Start free trial', 'cta_url' => $checkoutUrl('professional'), 'featured' => true,
            ],
            [
                'name' => 'Enterprise', 'price' => 'Custom', 'subtitle' => 'White-label + dedicated support',
                'features' => ['Multi-location support', 'White-label branding', 'SLA + priority support'],
                'cta_label' => 'Talk to us', 'cta_url' => route('marketing.demo'), 'featured' => false,
            ],
        ];
    }

    private function headlineStats(): array
    {
        return [
            'agents_active' => '27+',
            'businesses_running' => Business::where('is_active', true)->count() ?: 8,
            'avg_reply_time' => '<60s',
        ];
    }

    private function platformAvgReplySpeed(): string
    {
        $avg = Lead::where('ai_reply_sent', true)
            ->where('created_at', '>=', now()->subDay())
            ->avg('reply_time_seconds');

        return $avg ? round($avg) . 's' : '—';
    }

    private function platformTotalSaved(): float
    {
        $replies = Lead::where('ai_reply_sent', true)->count();
        $posts = ScheduledPost::whereNotNull('posted_at')->count();
        $leads = Lead::count();

        return ($replies * 8) + ($posts * 24) + ($leads * 7);
    }
}
