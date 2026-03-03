<?php

namespace Skywalker\Support\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class Health
{
    /**
     * Run a comprehensive health check.
     *
     * @return array
     */
    public static function check(): array
    {
        return [
            'status'      => static::isHealthy() ? 'ok' : 'error',
            'checks'      => [
                'database'   => static::checkDatabase(),
                'storage'    => static::checkStorage(),
                'env'        => static::checkEnv(),
                'php_version' => PHP_VERSION,
            ],
            'timestamp'   => now()->toIso8601String(),
        ];
    }

    /**
     * Check if system is overall healthy.
     *
     * @return bool
     */
    public static function isHealthy(): bool
    {
        return static::checkDatabase() && static::checkStorage();
    }

    /**
     * Check database connection.
     *
     * @return bool
     */
    protected static function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check storage permissions.
     *
     * @return bool
     */
    protected static function checkStorage(): bool
    {
        return File::isWritable(storage_path());
    }

    /**
     * Check for required ENV keys.
     *
     * @return array
     */
    protected static function checkEnv(): array
    {
        $required = ['APP_KEY', 'DB_CONNECTION'];
        $missing = [];

        foreach ($required as $key) {
            if (empty(env($key))) {
                $missing[] = $key;
            }
        }

        return [
            'status'  => empty($missing) ? 'ok' : 'warning',
            'missing' => $missing,
        ];
    }
}
