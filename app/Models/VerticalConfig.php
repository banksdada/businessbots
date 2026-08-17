<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerticalConfig extends Model
{
    protected $fillable = [
        'vertical_type', 'label', 'description',
        'default_tone', 'default_topics', 'default_hashtags', 'lead_questions',
    ];

    protected $casts = [
        'default_topics' => 'array',
        'default_hashtags' => 'array',
        'lead_questions' => 'array',
    ];
}
