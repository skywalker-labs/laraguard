<?php

namespace Skywalker\Support\Logging\Concerns;

use Illuminate\Support\Facades\Log;

trait HasContext
{
    /**
     * Log a message with context.
     *
     * @param  string  $level
     * @param  string  $message
     * @param  array  $context
     */
    protected function logWithContext(string $level, string $message, array $context = []): void
    {
        $defaultContext = [
            'request_id' => request()->header('X-Request-ID') ?? (string) \Illuminate\Support\Str::uuid(),
            'user_id'    => \Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::id() : null,
            'ip'         => request()->ip(),
        ];

        Log::log($level, $message, array_merge($defaultContext, $context));
    }
}
