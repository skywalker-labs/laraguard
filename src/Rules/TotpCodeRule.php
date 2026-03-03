<?php

namespace Skywalker\Laraguard\Rules;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Translation\Translator;
use Skywalker\Laraguard\Contracts\TwoFactorAuthenticatable;

class TotpCodeRule
{
    /**
     * The translator instance.
     *
     * @var \Illuminate\Contracts\Translation\Translator
     */
    protected $translator;

    /**
     * The authenticatable user.
     *
     * @var \Illuminate\Contracts\Auth\Authenticatable|null
     */
    protected $user;

    /**
     * Create a new "totp code" rule instance.
     *
     * @param  \Illuminate\Contracts\Translation\Translator  $translator
     * @param  \Illuminate\Contracts\Auth\Authenticatable|null  $user
     */
    public function __construct(Translator $translator, ?Authenticatable $user = null)
    {
        $this->translator = $translator;
        $this->user = $user;
    }

    /**
     * Validate that an attribute is a valid Two-Factor Authentication TOTP code.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validate($attribute, $value): bool
    {
        return is_string($value)
            && $this->user instanceof TwoFactorAuthenticatable
            && $this->user->validateTwoFactorCode($value);
    }
}


