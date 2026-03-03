<?php

namespace Skywalker\Support\Support\Concerns;

use ValueError;

trait Enum
{
    /**
     * Get all cases names.
     *
     * @return array
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get all cases values.
     *
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get an associative array of [value => name].
     *
     * @return array
     */
    public static function options(): array
    {
        return array_combine(self::values(), self::names());
    }

    /**
     * Try to get an Enum instance from a case name (key).
     *
     * @param  string  $key
     * @return static|null
     */
    public static function tryFromKey(string $key): ?static
    {
        foreach (self::cases() as $case) {
            if ($case->name === $key) {
                return $case;
            }
        }

        return null;
    }
}
