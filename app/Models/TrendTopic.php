<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrendTopic extends Model
{
    protected $fillable = ['keyword', 'source', 'detected_date', 'volume', 'used_count'];

    protected $casts = [
        'detected_date' => 'date',
    ];

    public function scopeToday($query)
    {
        return $query->whereDate('detected_date', today());
    }
}
