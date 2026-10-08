<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AutomationJob extends Model
{
    protected $fillable = [
        'uuid',
        'business_id',
        'user_id',
        'type',
        'status',
        'payload',
        'result',
        'error_message',
        'attempts',
        'queued_at',
        'started_at',
        'completed_at',
        'failed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'result' => 'array',
        'queued_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function problemRequest(): HasOne
    {
        return $this->hasOne(ProblemRequest::class);
    }
}
