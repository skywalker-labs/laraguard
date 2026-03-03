<?php

namespace Skywalker\Laraguard\Providers;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Routing\Router;
use Skywalker\Support\Providers\PackageServiceProvider;
use Skywalker\Laraguard\Contracts;
use Skywalker\Laraguard\Services;
use Skywalker\Laraguard\Events;
use Skywalker\Laraguard\Http;
use Skywalker\Laraguard\Listeners;
use Skywalker\Laraguard\View;
use Skywalker\Laraguard\Rules;
use Skywalker\Laraguard\Console;

class LaraguardServiceProvider extends PackageServiceProvider
{
    /**
     * Vendor name.
     *
     * @var string
     */
    protected $vendor = 'skywalker';

    /**
     * Package name.
     *
     * @var string
     */
    protected $package = 'laraguard';
    /**
     * The path of the migration file.
     *
     * @var string
     */
    protected const MIGRATION_FILE = __DIR__ . '/../../database/migrations/2020_04_02_000000_create_two_factor_authentications_table.php';
    protected const UPGRADE_FILE = __DIR__ . '/../../database/migrations/2020_04_02_000000_upgrade_two_factor_authentications_table.php';
    protected const LOG_MIGRATION_FILE = __DIR__ . '/../../database/migrations/2026_03_02_000000_create_two_factor_authentication_logs_table.php';
    protected const PASSKEY_MIGRATION_FILE = __DIR__ . '/../../database/migrations/2026_03_02_000001_create_two_factor_passkeys_table.php';
    protected const TRUSTED_DEVICE_MIGRATION_FILE = __DIR__ . '/../../database/migrations/2026_03_02_000002_create_two_factor_trusted_devices_table.php';
    protected const PANIC_MIGRATION_FILE = __DIR__ . '/../../database/migrations/2026_03_02_000003_add_panic_code_to_two_factor_authentications_table.php';
    protected const FINGERPRINT_MIGRATION_FILE = __DIR__ . '/../../database/migrations/2026_03_02_000004_add_hardware_id_to_two_factor_trusted_devices_table.php';

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        parent::register();

        $this->registerConfig();

        $this->app->singleton(Contracts\GeocodeProvider::class, Services\InternalGeocodeProvider::class);
    }

    /**
     * Resolve the base path of the package.
     *
     * @return string
     */
    protected function resolveBasePath()
    {
        return dirname(
            (new \ReflectionClass($this))->getFileName(),
            3
        );
    }

    /**
     * Get the base views path.
     *
     * @return string
     */
    protected function getViewsPath(): string
    {
        return __DIR__ . '/../../resources/views';
    }

    /**
     * Get the base translations path.
     *
     * @return string
     */
    protected function getTranslationsPath(): string
    {
        return __DIR__ . '/../../resources/lang';
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();

        $config = $this->app->make(Repository::class);
        $router = $this->app->make(Router::class);
        $validator = $this->app->make(Factory::class);

        $this->loadViews();
        $this->loadTranslations();

        $this->registerMiddleware($router);
        $this->registerRules($validator);
        $this->registerRoutes($config, $router);
        $this->registerBladeComponents();

        if ($this->app->runningInConsole()) {
            $this->publishAll();
            $this->commands([
                Console\LaraguardResetCommand::class,
                Console\LaraguardStatusCommand::class,
            ]);
        }

        $this->registerEventListeners();
    }

    /**
     * Register the event listeners.
     *
     * @return void
     */
    protected function registerEventListeners(): void
    {
        $events = [
            Events\TwoFactorEnabled::class,
            Events\TwoFactorDisabled::class,
            Events\TwoFactorFailed::class,
            Events\TwoFactorRecoveryCodesGenerated::class,
            Events\TwoFactorRecoveryCodesDepleted::class,
            Events\Panicked::class,
        ];

        foreach ($events as $event) {
            $this->app['events']->listen($event, Listeners\TwoFactorEventLogger::class);
            $this->app['events']->listen($event, Listeners\TwoFactorSecurityHub::class);
        }
    }

    /**
     * Register the middleware.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function registerMiddleware(Router $router): void
    {
        $router->aliasMiddleware('2fa.enabled', Http\Middleware\RequireTwoFactorEnabled::class);
        $router->aliasMiddleware('2fa.confirm', Http\Middleware\ConfirmTwoFactorCode::class);
        $router->aliasMiddleware('2fa.ratelimit', Http\Middleware\TwoFactorRateLimiter::class);
        $router->aliasMiddleware('2fa.geofence', Http\Middleware\TwoFactorGeofencing::class);
        $router->aliasMiddleware('2fa.honeypot', Http\Middleware\TwoFactorHoneyPot::class);
    }

    /**
     * Register the Blade components.
     *
     * @return void
     */
    protected function registerBladeComponents(): void
    {
        $this->callAfterResolving(\Illuminate\View\Factory::class, function () {
            if (class_exists(\Illuminate\Support\Facades\Blade::class)) {
                \Illuminate\Support\Facades\Blade::component('laraguard-qrcode', View\Components\QrCode::class);
                \Illuminate\Support\Facades\Blade::component('laraguard-status', View\Components\Status::class);
            }
        });
    }

    /**
     * Register custom validation rules.
     *
     * @param  \Illuminate\Contracts\Validation\Factory  $validator
     * @return void
     */
    protected function registerRules(Factory $validator): void
    {
        $validator->extendImplicit('totp_code', Rules\TotpCodeRule::class, trans('laraguard::validation.totp_code'));
    }

    /**
     * Register the routes for 2FA Code confirmation.
     *
     * @param  \Illuminate\Contracts\Config\Repository  $config
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function registerRoutes(Repository $config, Router $router): void
    {
        $prefix = $config->get('laraguard.god.stealth_prefix', '2fa');

        if ($view = $config->get('laraguard.confirm.view')) {
            $router->get($prefix . '/confirm', $view)->middleware(['web', '2fa.ratelimit'])->name('2fa.confirm');
        }

        if ($action = $config->get('laraguard.confirm.action')) {
            $router->post($prefix . '/confirm', $action)->middleware(['web', '2fa.ratelimit']);
        }

        // Passkey & God Routes (Elite/Legendary/God)
        $router->middleware('web')->group(function (Router $router) use ($prefix) {
            $router->get($prefix . '/passkey/register/options', 'Skywalker\Laraguard\Http\Controllers\PasskeyController@registrationOptions')->name('2fa.passkey.register.options');
            $router->post($prefix . '/passkey/register', 'Skywalker\Laraguard\Http\Controllers\PasskeyController@register')->name('2fa.passkey.register');
            $router->get($prefix . '/passkey/login/options', 'Skywalker\Laraguard\Http\Controllers\PasskeyController@authenticationOptions')->name('2fa.passkey.login.options');

            // Magic Link Routes (Legendary)
            $router->get($prefix . '/magic/generate', 'Skywalker\Laraguard\Http\Controllers\Magic2FAController@generate')->name('2fa.magic.generate');
            $router->get($prefix . '/magic/login/{id}', 'Skywalker\Laraguard\Http\Controllers\Magic2FAController@login')->name('2fa.magic.login');
        });
    }

    /**
     * Publish config, view and migrations files.
     *
     * @return void
     */
    protected function publishFiles(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/laraguard.php' => config_path('laraguard.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/laraguard'),
        ], 'views');

        $this->publishes([
            __DIR__ . '/../../resources/lang' => resource_path('lang/vendor/laraguard'),
        ], 'translations');

        $this->publishes([
            self::MIGRATION_FILE => database_path('migrations/'
                . now()->format('Y_m_d_His')
                . '_create_two_factor_authentications_table.php'),
        ], 'migrations');

        $this->publishes([
            self::UPGRADE_FILE => database_path('migrations/'
                . now()->format('Y_m_d_His')
                . '_upgrade_two_factor_authentications_table.php'),
        ], 'upgrade');

        $this->publishes([
            self::LOG_MIGRATION_FILE => database_path('migrations/'
                . now()->format('Y_m_d_His')
                . '_create_two_factor_authentication_logs_table.php'),
        ], 'logs');

        $this->publishes([
            self::PASSKEY_MIGRATION_FILE => database_path('migrations/'
                . now()->format('Y_m_d_His')
                . '_create_two_factor_passkeys_table.php'),
        ], 'passkeys');

        $this->publishes([
            self::TRUSTED_DEVICE_MIGRATION_FILE => database_path('migrations/'
                . now()->format('Y_m_d_His')
                . '_create_two_factor_trusted_devices_table.php'),
        ], 'trusted-devices');

        $this->publishes([
            self::PANIC_MIGRATION_FILE => database_path('migrations/'
                . now()->format('Y_m_d_His')
                . '_add_panic_code_to_two_factor_authentications_table.php'),
        ], 'panic-code');

        $this->publishes([
            self::FINGERPRINT_MIGRATION_FILE => database_path('migrations/'
                . now()->format('Y_m_d_His')
                . '_add_hardware_id_to_two_factor_trusted_devices_table.php'),
        ], 'fingerprint');
    }
}