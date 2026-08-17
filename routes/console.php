<?php

use App\Console\Commands\CleanupGeneratedImages;
use App\Console\Commands\RefreshChannelTokens;
use App\Jobs\PostSchedulerJob;
use Illuminate\Support\Facades\Schedule;

Schedule::command(RefreshChannelTokens::class)->twiceDaily();
Schedule::job(new PostSchedulerJob)->hourly();
Schedule::command(CleanupGeneratedImages::class)->daily();
