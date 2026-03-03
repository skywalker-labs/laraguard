<?php

namespace Skywalker\Laraguard\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface PasskeyAuthenticatable
{
    /**
     * Get the passkeys associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function passkeys(): MorphMany;

    /**
     * Determine if the user has passkeys registered.
     *
     * @return bool
     */
    public function hasPasskeysEnabled(): bool;
}
