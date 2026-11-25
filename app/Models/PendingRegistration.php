<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

/**
 * A sign-up waiting for its email OTP (see RegistrationService).
 */
class PendingRegistration extends Model
{
    use Prunable;

    /** How long a started sign-up stays valid before it must be restarted. */
    public const LIFETIME_HOURS = 24;

    /** How long one OTP is valid. */
    public const OTP_MINUTES = 10;

    /** Wrong codes allowed per OTP before a new one must be requested. */
    public const MAX_ATTEMPTS = 5;

    /** Minimum wait between two codes sent to the same email. */
    public const RESEND_SECONDS = 60;

    protected $fillable = [
        'email', 'name', 'password', 'mobile', 'country_id', 'role', 'fcm_token',
        'otp_hash', 'otp_expires_at', 'attempts', 'last_sent_at', 'expires_at',
    ];

    protected $hidden = ['password', 'otp_hash', 'fcm_token'];

    protected $casts = [
        'otp_expires_at' => 'datetime',
        'last_sent_at' => 'datetime',
        'expires_at' => 'datetime',
        'attempts' => 'integer',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Expired sign-ups are removed by `model:prune` (scheduled daily).
     */
    public function prunable(): Builder
    {
        return static::where('expires_at', '<', now());
    }
}
