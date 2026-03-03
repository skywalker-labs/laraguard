<?php

namespace Tests;

use Skywalker\Laraguard\Providers\LaraguardServiceProvider;

trait RegistersPackage
{
    protected function getPackageProviders($app)
    {
        return [LaraguardServiceProvider::class];
    }
}


