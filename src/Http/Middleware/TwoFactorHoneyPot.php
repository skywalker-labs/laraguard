<?php

namespace Skywalker\Laraguard\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Skywalker\Laraguard\Contracts\TwoFactorAuthenticatable;

class TwoFactorHoneyPot
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user instanceof TwoFactorAuthenticatable && $this->isUserPanicked($user)) {
            return $this->serveDecoy($request);
        }

        return $next($request);
    }

    /**
     * Determine if the user is in Panicked mode.
     *
     * @param  \Skywalker\Laraguard\Contracts\TwoFactorAuthenticatable  $user
     * @return bool
     */
    protected function isUserPanicked($user): bool
    {
        // We check the latest log for a Panicked event
        $latestLog = $user->twoFactorAuthLogs()->latest()->first();

        return $latestLog && $latestLog->event === 'Panicked';
    }

    /**
     * Serve decoy data to misdirect attackers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    protected function serveDecoy(Request $request)
    {
        $decoyData = config('laraguard.immortal.decoy_payload', [
            'status' => 'success',
            'balance' => 0.00,
            'message' => 'Action processed successfully.',
            'transactions' => []
        ]);

        return $request->expectsJson()
            ? Response::json($decoyData)
            : Response::make(view('laraguard::decoy', $decoyData));
    }
}
