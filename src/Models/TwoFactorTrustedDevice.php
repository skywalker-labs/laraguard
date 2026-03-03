<?php

namespace Skywalker\Laraguard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TwoFactorTrustedDevice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'device_id',
        'hardware_id',
        'ip_address',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Get the authenticatable model that owns the trusted device.
     */
    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Determine if the trusted device is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
