<?php

namespace Skywalker\Laraguard\Services;

use Illuminate\Support\Facades\Http;

class BreachIntelligence
{
    /**
     * Check if the email has been compromised in a breach.
     *
     * @param  string  $email
     * @return bool
     */
    public function isEmailCompromised(string $email): bool
    {
        $apiKey = config('laraguard.immortal.hibp_api_key');

        if (! $apiKey) {
            return false;
        }

        try {
            $response = Http::withHeaders(['hibp-api-key' => $apiKey])
                ->get('https://haveibeenpwned.com/api/v3/breachedaccount/' . urlencode($email));

            return $response->status() === 200;
        } catch (\Exception $e) {
            return false;
        }
    }
}
