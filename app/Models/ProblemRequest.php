<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A business problem a client submitted for AI-drafted, owner-reviewed advice.
 * The client only ever sees final_report, and only once status is approved.
 */
class ProblemRequest extends Model
{
    use HasFactory;

    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PENDING_REVIEW = 'pending_review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_FAILED = 'failed';

    public const URGENCIES = [
        'low' => 'Not urgent',
        'medium' => 'In the next few weeks',
        'high' => 'Urgent',
    ];

    public const HELP_OPTIONS = [
        'advice' => 'A plan I can follow myself',
        'build' => 'A plan, and help putting the fix in place',
    ];

    protected $fillable = [
        'business_id', 'user_id', 'automation_job_id',
        'title', 'description', 'already_tried', 'current_tools', 'staff_count',
        'urgency', 'help_wanted',
        'status', 'draft_report', 'final_report', 'error_message', 'approved_at',
    ];

    protected $casts = [
        'staff_count' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function automationJob(): BelongsTo
    {
        return $this->belongsTo(AutomationJob::class);
    }

    /** Wording the client sees. "Pending review" is still "being prepared" to them. */
    public function clientStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'Report ready',
            self::STATUS_FAILED => 'Delayed',
            default => 'Being prepared',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PROCESSING => 'AI drafting',
            self::STATUS_PENDING_REVIEW => 'Pending review',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_FAILED => 'Failed',
            default => ucfirst($this->status),
        };
    }

    public function isReady(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }
}
