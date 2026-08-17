<?php

namespace App\Jobs;

use App\Models\ChannelSetting;
use App\Models\ScheduledPost;
use App\Services\Posting\GbpPostingService;
use App\Services\Posting\InstagramPostingService;
use App\Services\Posting\LinkedInPostingService;
use App\Services\Posting\PostingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PostToChannelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(private readonly int $scheduledPostId) {}

    public function handle(): void
    {
        $post = ScheduledPost::find($this->scheduledPostId);

        // Post may have been deleted, or already published by an overlapping
        // run — both are fine to just skip rather than error on.
        if (! $post || $post->posted_at !== null) {
            return;
        }

        $channel = ChannelSetting::where('business_id', $post->business_id)
            ->where('platform', $post->platform)
            ->where('is_connected', true)
            ->first();

        if (! $channel) {
            \Log::warning('[PostToChannelJob] no connected channel — skipping post', [
                'post_id' => $post->id,
                'platform' => $post->platform,
            ]);
            return; // don't retry — retrying won't make a channel appear
        }

        try {
            $service = $this->resolveService($post->platform);
            $externalPostId = $service->post($post, $channel);

            $post->update([
                'posted_at' => now(),
                'post_id' => $externalPostId,
            ]);
        } catch (\Exception $e) {
            \Log::error('[PostToChannelJob] publish failed', [
                'post_id' => $post->id,
                'platform' => $post->platform,
                'error' => $e->getMessage(),
            ]);

            // Let ShouldQueue's tries/backoff retry transient failures (rate
            // limits, brief API outages) — but don't let one platform's outage
            // hold up the queue worker processing everyone else's posts.
            $this->release($this->backoff);
        }
    }

    private function resolveService(string $platform): PostingService
    {
        return match ($platform) {
            'instagram' => app(InstagramPostingService::class),
            'linkedin' => app(LinkedInPostingService::class),
            'gbp' => app(GbpPostingService::class),
            default => throw new \RuntimeException("No posting service registered for platform: {$platform}"),
        };
    }
}
