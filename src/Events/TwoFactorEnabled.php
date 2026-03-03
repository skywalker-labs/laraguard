<?php

namespace Skywalker\Laraguard\Events;

use Skywalker\Laraguard\Contracts\TwoFactorAuthenticatable;

class TwoFactorEnabled
{
    /**
     * The User using Two-Factor Authentication.
     *
     * @var \Skywalker\Laraguard\Contracts\TwoFactorAuthenticatable
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param  \Skywalker\Laraguard\Contracts\TwoFactorAuthenticatable  $user
     * @return void
     */
    public function __construct(TwoFactorAuthenticatable $user)
    {
        $this->user = $user;
    }
}


