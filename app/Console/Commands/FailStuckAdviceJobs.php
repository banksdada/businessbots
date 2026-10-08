<?php

namespace App\Console\Commands;

use App\Services\Advice\AdviceRequestService;
use Illuminate\Console\Command;

class FailStuckAdviceJobs extends Command
{
    protected $signature = 'portal:fail-stuck-jobs';

    protected $description = 'Mark client advice jobs with no result after the time limit as failed and email the owner';

    public function handle(AdviceRequestService $advice): int
    {
        $count = $advice->failStuckJobs();

        $this->info("Marked {$count} stuck job(s) as failed.");

        return self::SUCCESS;
    }
}
