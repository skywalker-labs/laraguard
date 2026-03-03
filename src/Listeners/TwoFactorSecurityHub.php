<?php

namespace Skywalker\Laraguard\Listeners;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Skywalker\Laraguard\Events\TwoFactorFailed;

class TwoFactorSecurityHub
{
    /**
     * Handle the event.
     *
     * @param  mixed  $event
     * @return void
     */
    public function handle($event)
    {
        $webhookUrl = config('laraguard.elite.webhook_url');

        if (!$webhookUrl) {
            return;
        }

        if ($event instanceof TwoFactorFailed) {
            $this->sendAlert($webhookUrl, [
                'type' => 'SECURITY_ALERT',
                'event' => '2FA_FAILURE',
                'user' => $event->user->getEmailForPasswordReset(),
                'ip' => request()->ip(),
                'severity' => 'HIGH',
            ]);
        }

        if ($event instanceof \Skywalker\Laraguard\Events\Panicked) {
            $this->sendAlert($webhookUrl, [
                'type' => 'DURESS_ALERT',
                'event' => 'PANIC_CODE_TRIGGERED',
                'user' => $event->user->getEmailForPasswordReset(),
                'ip' => request()->ip(),
                'severity' => 'CRITICAL',
            ]);
        }
    }

    /**
     * Send the alert to the configured webhook.
     *
     * @param  string  $url
     * @param  array   $data
     * @return void
     */
    protected function sendAlert(string $url, array $data)
    {
        try {
            Http::post($url, [
                'text' => "*Laraguard Security Alert* 🛡️\n" . 
                          "*Event*: " . $data['event'] . "\n" .
                          "*User*: " . $data['user'] . "\n" .
                          "*IP*: " . $data['ip'] . "\n" .
                          "*Severity*: " . $data['severity']
            ]);
        } catch (\Exception $e) {
            Log::error('Laraguard Security Hub failed to send alert: ' . $e->getMessage());
        }
    }
}
