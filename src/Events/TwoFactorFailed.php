<?php

namespace Skywalker\Laraguard\Events;

use Skywalker\Laraguard\Contracts\TwoFactorAuthenticatable;

class TwoFactorFailed
{
    /**
     * The User using Two-Factor Authentication.
     *
     * @var \Skywalker\Laraguard\Contracts\TwoFactorAuthenticatable
     */
    public $user;

    /**
     * The invalid code that was provided.
     *
     * @var string
     */
    public $code;

    /**
     * Create a new event instance.
     *
     * @param  \Skywalker\Laraguard\Contracts\TwoFactorAuthenticatable  $user
     * @param  string  $code
     * @return void
     */
    public function __construct(TwoFactorAuthenticatable $user, $code)
    {
        $this->user = $user;
        $this->code = $code;
    }
}
