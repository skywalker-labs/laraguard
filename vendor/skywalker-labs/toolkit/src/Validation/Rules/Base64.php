<?php

namespace Skywalker\Support\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Base64 implements ValidationRule
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
        if (! is_string($value) || ! preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $value)) {
            $fail('The :attribute must be a valid base64 encoded string.');
        }
    }
}
