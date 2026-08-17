<?php

namespace App\Livewire\Dashboard;

use App\Models\Business;
use App\Models\Lead;
use App\Models\ScheduledPost;
use Livewire\Component;

class Overview extends Component
{
    public Business $business;

    public int $leadsThisWeek = 0;
    public string $avgReplyTime = '—';
    public int $postsThisWeek = 0;
    public int $daysWithoutStaff = 0;
    public float $moneySavedThisMonth = 0;

    /** @var array<int, array{name: string, message: string, intent: string, status: string}> */
    public array $recentLeads = [];

    /** @var array<int, array{label: string, time: string, type: string}> */
    public array $recentActivity = [];

    public function mount(Business $business): void
    {
        $this->business = $business;
        $this->refreshStats();
    }

    /**
     * Re-pull dashboard aggregates. Runs on mount and every 30s via wire:poll.
     * Keep these queries cheap — indexed counts/averages only, no heavy joins.
     */
    public function refreshStats(): void
    {
        try {
            $this->leadsThisWeek = Lead::where('business_id', $this->business->id)
                ->where('created_at', '>=', now()->subWeek())
                ->count();

            $this->postsThisWeek = ScheduledPost::where('business_id', $this->business->id)
                ->whereNotNull('posted_at')
                ->where('posted_at', '>=', now()->subWeek())
                ->count();

            $this->avgReplyTime = $this->calculateAvgReplyTime();
            $this->daysWithoutStaff = $this->business->created_at->diffInDays(now());
            $this->moneySavedThisMonth = $this->estimateMonthlySavings();

            $this->recentLeads = Lead::where('business_id', $this->business->id)
                ->latest()
                ->take(5)
                ->get()
                ->map(fn (Lead $lead) => [
                    'name' => $lead->name ?? 'Unknown',
                    'message' => \Str::limit($lead->message, 60),
                    'intent' => $lead->intent,
                    'status' => $lead->status,
                ])
                ->toArray();
        } catch (\Exception $e) {
            \Log::error('[Dashboard\\Overview] refreshStats failed', [
                'business_id' => $this->business->id,
                'error' => $e->getMessage(),
            ]);
            // Leave prior values in place rather than blanking the dashboard on a transient error
        }
    }

    private function calculateAvgReplyTime(): string
    {
        $avgSeconds = Lead::where('business_id', $this->business->id)
            ->where('ai_reply_sent', true)
            ->where('created_at', '>=', now()->subDays(7))
            ->avg('reply_time_seconds');

        return $avgSeconds ? round($avgSeconds) . 's' : '—';
    }

    private function estimateMonthlySavings(): float
    {
        // Matches the "total saved" framing from the reference design —
        // replies + posts + leads, each priced against the staff/agency equivalent.
        $replies = Lead::where('business_id', $this->business->id)
            ->where('ai_reply_sent', true)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        $posts = ScheduledPost::where('business_id', $this->business->id)
            ->whereNotNull('posted_at')
            ->where('posted_at', '>=', now()->startOfMonth())
            ->count();

        return ($replies * 8) + ($posts * 24);
    }

    public function render()
    {
        return view('livewire.dashboard.overview');
    }
}
