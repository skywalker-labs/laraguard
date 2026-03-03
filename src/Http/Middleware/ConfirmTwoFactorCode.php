<?php

namespace Skywalker\Laraguard\Http\Middleware;

use Closure;
use Skywalker\Laraguard\Contracts\TwoFactorAuthenticatable;
use Illuminate\Http\Request;

use function response;
use function url;
use function auth;

class ConfirmTwoFactorCode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $redirectToRoute
     * @return mixed
     */
    public function handle($request, Closure $next, string $redirectToRoute = '2fa.confirm')
    {
        if ($this->userHasNotEnabledTwoFactorAuth() || $this->codeWasValidated($request) || $this->deviceIsTrusted($request)) {
            return $next($request);
        }

        return $request->expectsJson()
            ? response()->json(['message' => trans('laraguard::messages.required')], 403)
            : response()->redirectGuest(url()->route($redirectToRoute));
    }

    /**
     * Determine if the current device is trusted.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function deviceIsTrusted(Request $request): bool
    {
        $user = auth()->user();

        return $user instanceof TwoFactorAuthenticatable && $user->isDeviceTrusted($request);
    }

    /**
     * Check if the user is using Two-Factor Authentication.
     *
     * @return bool
     */
    protected function userHasNotEnabledTwoFactorAuth(): bool
    {
        $user = auth()->user();

        return ! ($user instanceof TwoFactorAuthenticatable && $user->hasTwoFactorEnabled());
    }

    /**
     * Determine if the confirmation timeout has expired.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function codeWasValidated(Request $request): bool
    {
        $confirmedAt = now()->timestamp - $request->session()->get('2fa.totp_confirmed_at', 0);

        return $confirmedAt < config('laraguard.confirm.timeout', 10800);
    }
}


