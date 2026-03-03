<?php

namespace Skywalker\Laraguard\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Skywalker\Laraguard\Eloquent\TwoFactorAuthenticationLog;

class FilamentLaraguardPlugin implements Plugin
{
    /**
     * Get the plugin ID.
     */
    public function getId(): string
    {
        return 'laraguard';
    }

    /**
     * Register the plugin in the panel.
     */
    public function register(Panel $panel): void
    {
        // We could register custom pages or resources here
        // For example, a "Security Audit Log" resource
    }

    /**
     * Bootstrap the plugin.
     */
    public function boot(Panel $panel): void
    {
        // 
    }

    /**
     * Static helper to make basic plugin instance.
     */
    public static function make(): static
    {
        return app(static::class);
    }
}
