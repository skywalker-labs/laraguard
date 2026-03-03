<?php

namespace Tests\Stubs;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Skywalker\Laraguard\Traits\TwoFactorAuthentication;
use Skywalker\Laraguard\Contracts\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class UserTwoFactorStub extends UserStub implements TwoFactorAuthenticatable
{
    use TwoFactorAuthentication;
}


