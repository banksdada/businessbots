<?php

namespace App\Jobs;

use App\Models\ScheduledPost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PostSchedulerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Runs hourly (see SETUP-NOTES.md scheduler section). Deliberately thin —
     * find what's due, dispatch one job per post. All the actual publishing
     * logic and error handling lives in PostToChannelJob, one queue job per
     * post, so a failure on post #47 can't block posts #48-200 from going out.
     */
    public function handle(): void
    {
        try {
            $duePosts = ScheduledPost::due()->get(['id']);

            foreach ($duePosts as $post) {
                PostToChannelJob::dispatch($post->id);
            }

            \Log::info('[PostSchedulerJob] dispatched posts', ['count' => $duePosts->count()]);
        } catch (\Exception $e) {
            \Log::error('[PostSchedulerJob] failed to query due posts', ['error' => $e->getMessage()]);
        }
    }
}
