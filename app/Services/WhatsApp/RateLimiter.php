<?php

namespace App\Services\WhatsApp;

use Illuminate\Support\Facades\Cache;

class RateLimiter
{
    /** Matches the limits from architecture.md: 1/min per number, 100/hour per business. */
    public function tooManyForNumber(int $businessId, string $phone): bool
    {
        $key = "whatsapp:rate:number:{$businessId}:{$phone}";

        if (Cache::has($key)) {
            return true;
        }

        Cache::put($key, true, now()->addMinute());
        return false;
    }

    public function tooManyForBusiness(int $businessId): bool
    {
        $key = "whatsapp:rate:business:{$businessId}";
        $count = Cache::get($key, 0);

        if ($count >= 100) {
            return true;
        }

        Cache::put($key, $count + 1, now()->addHour());
        return false;
    }
}
