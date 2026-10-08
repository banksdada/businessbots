<?php

use App\Console\Commands\CleanupGeneratedImages;
use App\Console\Commands\FailStuckAdviceJobs;
use App\Console\Commands\RefreshChannelTokens;
use App\Jobs\PostSchedulerJob;
use Illuminate\Support\Facades\Schedule;

Schedule::command(FailStuckAdviceJobs::class)->everyTenMinutes();

// Social posting and channel tokens — paused unless FEATURE_SOCIAL=true.
if (config('features.social')) {
    Schedule::command(RefreshChannelTokens::class)->twiceDaily();
    Schedule::job(new PostSchedulerJob)->hourly();
    Schedule::command(CleanupGeneratedImages::class)->daily();
}
