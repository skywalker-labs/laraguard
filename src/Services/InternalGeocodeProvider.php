<?php

namespace Skywalker\Laraguard\Services;

use Skywalker\Laraguard\Contracts\GeocodeProvider;

class InternalGeocodeProvider implements GeocodeProvider
{
    /**
     * Detect the country code from an IP address.
     *
     * @param  string  $ip
     * @return string
     */
    public function detectCountry(string $ip): string
    {
        // Simple internal mock. In production, users can swap this for MaxMind or IPStack.
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return 'IN';
        }

        return 'UNKNOWN';
    }
}
