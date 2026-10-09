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

    /** What the problem costs the client. Ticked on the form; tells us where the value is. */
    public const IMPACTS = [
        'time' => 'Staff time',
        'money' => 'Money',
        'mistakes' => 'Mistakes or things missed',
        'customers' => 'Missed enquiries or clients',
        'stress' => 'Stress or lost focus',
        'compliance' => 'Compliance worries',
    ];

    protected $fillable = [
        'business_id', 'user_id', 'automation_job_id',
        'title', 'description', 'impacts', 'hours_per_week', 'hourly_cost', 'desired_outcome', 'already_tried', 'current_tools', 'staff_count',
        'urgency', 'help_wanted',
        'status', 'draft_report', 'final_report', 'error_message', 'approved_at',
    ];

    protected $casts = [
        'staff_count' => 'integer',
        'impacts' => 'array',
        'hours_per_week' => 'float',
        'hourly_cost' => 'integer',
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

    /** The ticked impacts as readable labels, e.g. "Staff time, Money". */
    public function impactLabels(): ?string
    {
        $labels = collect($this->impacts ?? [])->map(fn ($key) => self::IMPACTS[$key] ?? $key);

        return $labels->isEmpty() ? null : $labels->implode(', ');
    }

    /**
     * The client's own figures scaled up to a year, e.g. "about 156 hours (£2,340) a year".
     * Null when they gave no hours, so we never guess.
     */
    public static function yearlyCostText(?float $hoursPerWeek, ?int $hourlyCost): ?string
    {
        if (! $hoursPerWeek) {
            return null;
        }

        $hours = (int) round($hoursPerWeek * 52);
        $text = 'about ' . number_format($hours) . ' hours';

        if ($hourlyCost) {
            $text .= ' (£' . number_format($hours * $hourlyCost) . ')';
        }

        return $text . ' a year';
    }

    public function yearlyCost(): ?string
    {
        return self::yearlyCostText($this->hours_per_week, $this->hourly_cost);
    }

    /** Wording the client sees. "Pending review" is still "being prepared" to them. */
    public function clientStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'Plan ready',
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
