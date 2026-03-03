<?php

namespace Skywalker\Laraguard\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class TwoFactorGeofencing
{
    /**
     * The Geocode support.
     *
     * @var \Skywalker\Laraguard\Contracts\GeocodeProvider
     */
    protected $geocoder;

    /**
     * Create a new middleware instance.
     *
     * @param  \Skywalker\Laraguard\Contracts\GeocodeProvider  $geocoder
     * @return void
     */
    public function __construct(\Skywalker\Laraguard\Contracts\GeocodeProvider $geocoder)
    {
        $this->geocoder = $geocoder;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $allowedCountries
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $allowedCountries = null)
    {
        $allowed = $allowedCountries ? explode(',', $allowedCountries) : config('laraguard.god.allowed_countries', []);

        if (empty($allowed)) {
            return $next($request);
        }

        $country = $this->geocoder->detectCountry($request->ip());

        if (!in_array($country, $allowed)) {
            return $request->expectsJson()
                ? Response::json(['error' => 'Access denied from your region (' . $country . ')'], 403)
                : abort(403, 'Access denied from your region.');
        }

        return $next($request);
    }
}
