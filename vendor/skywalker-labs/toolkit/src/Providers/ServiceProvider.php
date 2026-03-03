<?php


namespace Skywalker\Support\Providers;

use Skywalker\Support\Providers\Concerns\InteractsWithApplication;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * Class     ServiceProvider
 *
 * @author   Skywalker <skywalker@example.com>
 */
abstract class ServiceProvider extends IlluminateServiceProvider
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use InteractsWithApplication;
}
