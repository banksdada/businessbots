<?php

namespace App\Console\Commands;

use App\Models\ChannelSetting;
use App\Services\Channels\ChannelTokenRefresher;
use Illuminate\Console\Command;

class RefreshChannelTokens extends Command
{
    protected $signature = 'channels:refresh-tokens';
    protected $description = 'Refresh OAuth tokens for connected channels expiring within 3 days';

    public function handle(ChannelTokenRefresher $refresher): int
    {
        $expiringSoon = ChannelSetting::where('is_connected', true)
            ->whereNotNull('token_expires_at')
            ->where('token_expires_at', '<=', now()->addDays(3))
            ->get();

        $this->info("Found {$expiringSoon->count()} channel(s) expiring within 3 days.");

        $refreshed = 0;
        $failed = 0;

        foreach ($expiringSoon as $channel) {
            $refresher->refresh($channel) ? $refreshed++ : $failed++;
        }

        $this->info("Refreshed: {$refreshed}, failed (now disconnected): {$failed}");

        if ($failed > 0) {
            // TODO: notify affected business owners their channel needs reconnecting —
            // wire up a Notification here once the mail/notification layer exists.
            \Log::warning('[RefreshChannelTokens] some channels disconnected after failed refresh', ['count' => $failed]);
        }

        return self::SUCCESS;
    }
}
