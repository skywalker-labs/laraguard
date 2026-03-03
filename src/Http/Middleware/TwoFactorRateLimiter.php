<?php

namespace Skywalker\Laraguard\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Response;
use Skywalker\Laraguard\Events\TwoFactorFailed;

class TwoFactorRateLimiter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $maxAttempts
     * @param  int  $decayMinutes
     * @return mixed
     */
    public function handle(Request $request, Closure $next, int $maxAttempts = 5, int $decayMinutes = 1)
    {
        $key = '2fa_attempts:' . ($request->user()?->getAuthIdentifier() ?: $request->ip());

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);

            return $request->expectsJson()
                ? Response::json(['error' => 'Too many failed 2FA attempts. Please try again in ' . $seconds . ' seconds.'], 429)
                : back()->withErrors(['2fa_code' => 'Too many attempts. Please wait ' . $seconds . ' seconds.']);
        }

        return $next($request);
    }
}
