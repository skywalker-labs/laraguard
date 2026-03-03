<?php

namespace Skywalker\Laraguard\Listeners;

use Illuminate\Http\Request;
use Skywalker\Laraguard\Eloquent\TwoFactorAuthenticationLog;

class TwoFactorEventLogger
{
    /**
     * The current request.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Create a new listener instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param  mixed  $event
     * @return void
     */
    public function handle($event)
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('two_factor_authentication_logs')) {
            return;
        }

        $user = $event->user;

        $logData = [
            'event'      => class_basename($event),
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'created_at' => now(),
        ];

        // Anomaly Detection: IP Shift
        $lastLog = $user->twoFactorAuthLogs()->latest()->first();
        if ($lastLog && $lastLog->ip_address !== $this->request->ip()) {
            $logData['metadata'] = [
                'anomaly' => 'IP_SHIFT',
                'previous_ip' => $lastLog->ip_address,
                'severity' => 'MEDIUM',
            ];
            // In a real Legendary implementation, we could trigger a specific alert here
        }

        // Capture additional metadata if available
        if (isset($event->code)) {
            $logData['payload'] = ['code' => $event->code];
        }

        if ($event instanceof \Skywalker\Laraguard\Events\Panicked) {
            $logData['metadata'] = array_merge($logData['metadata'] ?? [], [
                'severity' => 'CRITICAL',
                'duress' => true
            ]);
        }

        if ($user->twoFactorAuth()->exists()) {
            $user->twoFactorAuthLogs()->create($logData);
        } else {
            // If TwoFactorAuthentication model doesn't exist yet (very rare, e.g. during failed enable before secret creation),
            // or if we just want to log against the authenticatable directly.
            $logRecord = new TwoFactorAuthenticationLog($logData);
            $logRecord->authenticatable()->associate($user);
            $logRecord->save();
        }

        // Increment Rate Limiter if it's a failure event
        if ($event instanceof \Skywalker\Laraguard\Events\TwoFactorFailed) {
            $key = '2fa_attempts:' . ($user->getAuthIdentifier() ?: $this->request->ip());
            \Illuminate\Support\Facades\RateLimiter::hit($key, 60);
        }
    }
}
