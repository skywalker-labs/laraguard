<?php

namespace Skywalker\Support\Support\Macros;

use Illuminate\Support\Str;

class StringMacros
{
    /**
     * Register the macros.
     */
    public static function register(): void
    {
        Str::macro('isBase64', function ($value) {
            return (bool) preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $value);
        });
    }
}
