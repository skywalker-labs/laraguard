<?php

namespace Skywalker\Laraguard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TwoFactorPasskey extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'two_factor_passkeys';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'counter'      => 'int',
        'last_used_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'credential_id',
        'public_key',
        'nickname',
        'counter',
        'user_handle',
        'last_used_at',
    ];

    /**
     * Get the authenticatable model that the passkey belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function authenticatable(): MorphTo
    {
        return $this->morphTo('authenticatable');
    }
}
