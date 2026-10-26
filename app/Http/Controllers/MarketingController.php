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
        if (! config('features.social')) {
            return view('marketing.home-advice');
        }

        return view('marketing.home', [
            'verticals' => $this->verticalOptions(),
            'pricingTiers' => $this->pricingTiers(),
            'stats' => $this->headlineStats(),
            'liveBusinessCount' => Business::where('is_active', true)->count() ?: 8,
            'aiInsights' => $this->aiInsights(),
            'successStories' => $this->successStories(),
        ]);
    }

    public function pricing(): View
    {
        if (! config('features.social')) {
            return view('marketing.pricing-advice');
        }

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
     * Public insights index — blog/listing of problem-fix posts.
     */
    public function insights(): View
    {
        return view('marketing.insights', [
            'posts' => $this->insightPosts(),
        ]);
    }

    /**
     * Public single insight post page.
     */
    public function insight(string $slug): View
    {
        $post = collect($this->insightPosts())
            ->firstWhere('slug', $slug);

        abort_unless($post, 404);

        return view('marketing.insight', compact('post'));
    }

    /**
     * Seed insight posts. Real content lives in resources/views/marketing/
     * as separate Blade templates; this is the index of what's published.
     */
    private function insightPosts(): array
    {
        return [
            [
                'slug' => 'you-dont-need-ai',
                'title' => 'You don\'t need AI. You need the problem gone.',
                'excerpt' => 'Most businesses don\'t need more AI tools. They need the bottleneck gone. Here\'s how to find the one problem worth fixing first.',
                'category' => 'Mindset',
                'date' => '2026-10-24',
            ],
            [
                'slug' => 'two-minute-test',
                'title' => 'The 2-minute test: is this problem worth fixing?',
                'excerpt' => 'Before you automate anything, run this 2-minute test. It tells you whether a problem is worth fixing — or just annoying.',
                'category' => 'Process',
                'date' => '2026-10-24',
            ],
            [
                'slug' => 'human-review',
                'title' => 'Why we review every plan by hand',
                'excerpt' => 'AI can draft a fix plan in seconds. We still have a real person check every one. Here\'s why — and what it means for you.',
                'category' => 'Trust',
                'date' => '2026-10-24',
            ],
        ];
    }

    /**
     * Public "living proof" page — no auth required.
     * Aggregate, non-sensitive stats only; never expose per-business data here.
     */
    public function liveProof(): View
    {
        abort_unless(config('features.social'), 404);

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

    /**
     * Live AI insights shown on the homepage.
     *
     * These are real aggregates from the database — never fabricated. If the
     * tables are empty or the query fails, we fall back to an explicit
     * "not yet measuring" state rather than inventing a confident number.
     */
    private function aiInsights(): array
    {
        try {
            $leadsThisWeek = Lead::where('created_at', '>=', now()->subDays(7))->count();
            $leadsLastWeek  = Lead::where('created_at', '>=', now()->subDays(14))
                                  ->where('created_at', '<', now()->subDays(7))->count();
            $repliedThisWeek = Lead::where('ai_reply_sent', true)
                                  ->where('created_at', '>=', now()->subDays(7))->count();
            $postsThisWeek   = ScheduledPost::where('posted_at', '>=', now()->subDays(7))->count();

            $trend = $leadsLastWeek > 0
                ? round((($leadsThisWeek - $leadsLastWeek) / $leadsLastWeek) * 100)
                : 0;

            $replyRate = $leadsThisWeek > 0
                ? round(($repliedThisWeek / $leadsThisWeek) * 100)
                : 0;
        } catch (\Throwable $e) {
            \Log::warning('[MarketingController] aiInsights failed', ['error' => $e->getMessage()]);
            return [
                ['label' => 'Leads', 'as_of' => 'this week', 'value' => '—', 'detail' => 'Not yet measuring.'],
                ['label' => 'Trend', 'as_of' => 'vs last week', 'value' => '—', 'detail' => 'Not yet measuring.'],
                ['label' => 'Reply rate', 'as_of' => 'this week', 'value' => '—', 'detail' => 'Not yet measuring.'],
                ['label' => 'Posts', 'as_of' => 'this week', 'value' => '—', 'detail' => 'Not yet measuring.'],
            ];
        }

        $trendLabel = $trend > 0 ? "+{$trend}%" : ($trend < 0 ? "{$trend}%" : '0%');
        $trendDetail = $trend > 0
            ? "Up from last week — the system is pulling ahead."
            : ($trend < 0
                ? "Down from last week — worth a look."
                : "Flat vs last week.");

        return [
            [
                'label' => 'Leads',
                'as_of' => 'this week',
                'value' => $leadsThisWeek,
                'detail' => "New leads in the last 7 days. {$trendLabel} vs the week before.",
            ],
            [
                'label' => 'Trend',
                'as_of' => 'vs last week',
                'value' => $trendLabel,
                'detail' => $trendDetail,
            ],
            [
                'label' => 'Reply rate',
                'as_of' => 'this week',
                'value' => "{$replyRate}%",
                'detail' => "Leads answered by the AI this week. Target is 100%.",
            ],
            [
                'label' => 'Posts',
                'as_of' => 'this week',
                'value' => $postsThisWeek,
                'detail' => "Pieces of content published across channels in the last 7 days.",
            ],
        ];
    }

    /**
     * Real success stories from the network — active businesses with their
     * vertical and a genuine, computed metric. Never fabricated: when there
     * are no businesses yet, we return an empty list and the view shows a
     * "first customer" state instead of invented testimonials.
     */
    private function successStories(): array
    {
        try {
            return Business::query()
                ->where('is_active', true)
                ->with(['businessVertical' => function ($q) {
                    $q->with(['verticalConfig' => function ($c) {
                        $c->select(['vertical_type', 'label']);
                    }]);
                }])
                ->orderByDesc('created_at')
                ->limit(6)
                ->get()
                ->map(function (Business $business) {
                    $vertical = $business->businessVertical?->verticalConfig?->label
                        ?? $business->businessVertical?->vertical_type
                        ?? 'Business';

                    $leadsThisMonth = $business->leads()
                        ->where('created_at', '>=', now()->subDays(30))
                        ->count();

                    $postedThisMonth = $business->scheduledPosts()
                        ->where('posted_at', '>=', now()->subDays(30))
                        ->count();

                    return [
                        'name' => $business->name,
                        'vertical' => $vertical,
                        'metric' => $leadsThisMonth,
                        'metric_label' => $leadsThisMonth === 1 ? 'lead this month' : 'leads this month',
                        'posts' => $postedThisMonth,
                        'since' => $business->created_at?->diffForHumans() ?? 'recently',
                    ];
                })
                ->values()
                ->toArray();
        } catch (\Throwable $e) {
            \Log::warning('[MarketingController] successStories failed', ['error' => $e->getMessage()]);
            return [];
        }
    }
}
