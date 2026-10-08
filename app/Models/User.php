<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

class User extends Authenticatable implements FilamentUser, HasTenants
{
    use HasFactory, Notifiable, Billable;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    /**
     * Filament's FilamentUser contract. Outside local dev, Filament returns 403
     * to anyone not allowed here. /admin is each client's own business panel;
     * /ops is the owner's review panel for every client's requests.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'ops' => $this->is_admin,
            default => true,
        };
    }

    public function problemRequests(): HasMany
    {
        return $this->hasMany(ProblemRequest::class);
    }

    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }

    /**
     * The business currently driving onboarding/dashboard state.
     * Cached per-request — call once per request cycle, not in a loop.
     */
    public function activeBusiness(): ?Business
    {
        return $this->businesses()->where('is_active', true)->first();
    }

    /**
     * Filament's HasTenants contract — the businesses this user can switch
     * between in the admin panel. Only onboarded (is_active) businesses are
     * offered as tenants; a business mid-onboarding isn't ready for admin use.
     */
    public function getTenants(Panel $panel): \Illuminate\Support\Collection
    {
        return $this->businesses()->where('is_active', true)->get();
    }

    public function canAccessTenant(\Illuminate\Database\Eloquent\Model $tenant): bool
    {
        return $this->businesses()->where('id', $tenant->getKey())->exists();
    }
}
