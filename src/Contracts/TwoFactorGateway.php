<?php

namespace Skywalker\Laraguard\Contracts;

interface TwoFactorGateway
{
    /**
     * Send the given code to the user.
     *
     * @param  \Skywalker\Laraguard\Contracts\TwoFactorAuthenticatable  $user
     * @param  string  $code
     * @return bool
     */
    public function send(TwoFactorAuthenticatable $user, string $code): bool;
}
