<?php

namespace Skywalker\Support\Database\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

class MoneyCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed   $value
     * @param  array   $attributes
     * @return float
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return $value / 100;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed   $value
     * @param  array   $attributes
     * @return int
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if (! is_numeric($value)) {
            throw new InvalidArgumentException('Money value must be numeric.');
        }

        return (int) round($value * 100);
    }
}
