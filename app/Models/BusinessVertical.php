<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessVertical extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'vertical_type', 'industry_config'];

    protected $casts = [
        'industry_config' => 'array',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Merge this business's overrides on top of the vertical's shared defaults.
     * Used by the content generator to build vertical-aware prompts.
     */
    public function resolvedConfig(): array
    {
        $default = VerticalConfig::where('vertical_type', $this->vertical_type)->first();

        return array_merge(
            $default?->toArray() ?? [],
            array_filter($this->industry_config ?? [])
        );
    }
}
