<?php

namespace App\Console\Commands;

use App\Models\LoginToken;
use App\Services\Crm\CrmCaptureService;
use Illuminate\Console\Command;

class BackfillCrm extends Command
{
    protected $signature = 'crm:backfill {--dry-run : List the verified sign-ups without sending them}';

    protected $description = 'Send previously verified sign-ups to the AIOS CRM';

    public function handle(CrmCaptureService $crm): int
    {
        $users = LoginToken::query()
            ->whereNotNull('used_at')
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter()
            ->unique('id')
            ->values();

        if ($users->isEmpty()) {
            $this->info('No verified sign-ups found.');

            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            foreach ($users as $user) {
                $this->line("{$user->name} <{$user->email}>");
            }

            $this->info("Would capture {$users->count()} verified sign-ups.");

            return self::SUCCESS;
        }

        $sent = 0;

        foreach ($users as $user) {
            if ($crm->capture($user)) {
                $sent++;
            }
        }

        $this->info("Captured {$sent} of {$users->count()} verified sign-ups.");

        return self::SUCCESS;
    }
}
