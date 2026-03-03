<?php

namespace Skywalker\Laraguard\Contracts;

interface GeocodeProvider
{
    /**
     * Detect the country code from an IP address.
     *
     * @param  string  $ip
     * @return string
     */
    public function detectCountry(string $ip): string;
}
