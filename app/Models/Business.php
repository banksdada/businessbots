<?php

namespace App\Models;

use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Business extends Model implements HasName
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'location', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function businessVertical(): HasOne
    {
        return $this->hasOne(BusinessVertical::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function scheduledPosts(): HasMany
    {
        return $this->hasMany(ScheduledPost::class);
    }

    public function channelSettings(): HasMany
    {
        return $this->hasMany(ChannelSetting::class);
    }

    public function aiTemplates(): HasMany
    {
        return $this->hasMany(AiTemplate::class);
    }

    public function verticalType(): ?string
    {
        return $this->businessVertical?->vertical_type;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** Filament's HasName contract — label shown in the tenant (business) switcher. */
    public function getFilamentName(): string
    {
        return $this->name ?? "Business #{$this->id}";
    }
}
