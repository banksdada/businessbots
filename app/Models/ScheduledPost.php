<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id', 'platform', 'caption', 'hashtags', 'media_url',
        'scheduled_time', 'posted_at', 'post_id',
        'likes', 'comments', 'shares', 'reach', 'impressions',
        'engagement_rate', 'performance_score',
    ];

    protected $casts = [
        'scheduled_time' => 'datetime',
        'posted_at' => 'datetime',
        'engagement_rate' => 'float',
        'performance_score' => 'float',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function scopeDue($query)
    {
        return $query->where('scheduled_time', '<=', now())->whereNull('posted_at');
    }

    public function scopePosted($query)
    {
        return $query->whereNotNull('posted_at');
    }
}
