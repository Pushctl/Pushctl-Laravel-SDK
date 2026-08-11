<?php

declare(strict_types=1);

namespace Flowgistics\Pushctl\Tests;

use Flowgistics\Pushctl\PushctlServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            PushctlServiceProvider::class,
        ];
    }
}
