<?php


namespace Skywalker\Support\Tests\Stubs;

use Skywalker\Support\Providers\RouteServiceProvider;

/**
 * Class     RouteServiceProviderWithRouteClasses
 *
 * @author   Skywalker <skywalker@example.com>
 */
class RouteServiceProviderWithRouteClasses extends RouteServiceProvider
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    protected $routesClasses = [
        \Skywalker\Support\Tests\Stubs\PagesRoutes::class,
    ];

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        parent::boot();

        static::bindRouteClasses($this->routesClasses);
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        static::mapRouteClasses($this->routesClasses);

        //
    }
}
