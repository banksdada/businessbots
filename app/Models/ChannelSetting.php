<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsEncryptedCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChannelSetting extends Model
{
    protected $fillable = [
        'business_id', 'platform', 'access_token', 'refresh_token',
        'token_expires_at', 'external_account_id', 'external_account_name',
        'is_connected',
    ];

    protected $casts = [
        // Laravel encrypts/decrypts transparently on read/write — tokens are
        // never stored or logged in plaintext. See code-standards.md security section.
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
        'token_expires_at' => 'datetime',
        'is_connected' => 'boolean',
    ];

    protected $hidden = ['access_token', 'refresh_token'];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function isExpired(): bool
    {
        return $this->token_expires_at !== null && $this->token_expires_at->isPast();
    }
}
