<?php

namespace Skywalker\Laraguard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TwoFactorAuthenticationLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'two_factor_authentication_logs';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'payload'    => 'json',
        'metadata'   => 'json',
        'created_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event',
        'ip_address',
        'user_agent',
        'payload',
        'created_at',
    ];

    /**
     * The model that the log belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function authenticatable(): MorphTo
    {
        return $this->morphTo('authenticatable');
    }
}
