<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id', 'phone', 'name', 'message', 'intent',
        'ai_reply_sent', 'ai_reply_text', 'reply_time_seconds',
        'status', 'opted_out', 'opted_out_at', 'consent_notice_sent_at',
        'scheduled_visit_at',
    ];

    protected $casts = [
        'ai_reply_sent' => 'boolean',
        'opted_out' => 'boolean',
        'opted_out_at' => 'datetime',
        'consent_notice_sent_at' => 'datetime',
        'scheduled_visit_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(WhatsAppConversation::class);
    }

    /** Complaints and unreplied messages surface for manual follow-up in the UI. */
    public function needsHumanAttention(): bool
    {
        return $this->intent === 'complaint' || ! $this->ai_reply_sent;
    }

    public function scopeForBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    /**
     * Has this phone number opted out of AI replies for this business, ever?
     * Checked before every send — an opt-out on one conversation thread must
     * suppress replies across all future messages from the same number.
     */
    public static function hasOptedOut(int $businessId, string $phone): bool
    {
        return static::where('business_id', $businessId)
            ->where('phone', $phone)
            ->where('opted_out', true)
            ->exists();
    }
}
