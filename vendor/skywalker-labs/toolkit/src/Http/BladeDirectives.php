<?php

namespace Skywalker\Support\Http;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;

class BladeDirectives
{
    /**
     * Register the Blade directives.
     */
    public static function register(): void
    {
        Blade::directive('active', function ($expression) {
            return "<?php echo Skywalker\Support\Http\BladeDirectives::isActive($expression) ? 'active' : ''; ?>";
        });

        Blade::directive('money', function ($expression) {
            return "<?php echo Skywalker\Support\Http\BladeDirectives::formatMoney($expression); ?>";
        });

        Blade::directive('date', function ($expression) {
            return "<?php echo Skywalker\Support\Http\BladeDirectives::formatDate($expression); ?>";
        });
    }

    /**
     * Check if the current route is active.
     *
     * @param  string  $route
     * @return bool
     */
    public static function isActive(string $route): bool
    {
        return Route::is($route);
    }

    /**
     * Format an amount of money.
     *
     * @param  string|float|int  $amount
     * @param  string  $currency
     * @return string
     */
    public static function formatMoney($amount, string $currency = 'USD'): string
    {
        return number_format((float) $amount, 2) . ' ' . $currency;
    }

    /**
     * Format a date.
     *
     * @param  mixed  $date
     * @param  string  $format
     * @return string
     */
    public static function formatDate($date, string $format = 'Y-m-d H:i:s'): string
    {
        return \Illuminate\Support\Carbon::parse($date)->format($format);
    }
}
