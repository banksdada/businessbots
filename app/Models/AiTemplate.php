<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiTemplate extends Model
{
    protected $fillable = ['business_id', 'intent_type', 'template_text', 'performance_score'];

    protected $casts = [
        'performance_score' => 'float',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
