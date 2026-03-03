<?php

namespace Skywalker\Support\Http\Client;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;

class Client
{
    /**
     * Create a new pending request instance.
     *
     * @return \Illuminate\Http\Client\PendingRequest
     */
    public static function create(): PendingRequest
    {
        return Http::withHeaders([
            'User-Agent' => 'Skywalker/Support v1.0',
            'Accept'     => 'application/json',
        ]);
    }

    /**
     * Proxy static method calls to Http facade.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return static::create()->$method(...$parameters);
    }
}
