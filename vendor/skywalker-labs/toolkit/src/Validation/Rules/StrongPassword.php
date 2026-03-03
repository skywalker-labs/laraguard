<?php

namespace Skywalker\Support\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute must be a string.');
            return;
        }

        $length = strlen($value) >= 8;
        $uppercase = preg_match('/[A-Z]/', $value);
        $lowercase = preg_match('/[a-z]/', $value);
        $number = preg_match('/[0-9]/', $value);
        $special = preg_match('/[!@#$%^&*(),.?":{}|<>]/', $value);

        if (! ($length && $uppercase && $lowercase && $number && $special)) {
            $fail('The :attribute must be at least 8 characters and contain at least one uppercase letter, one lowercase letter, one number, and one special character.');
        }
    }
}
