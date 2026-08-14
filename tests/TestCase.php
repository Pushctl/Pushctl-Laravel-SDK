<?php

declare(strict_types=1);

namespace Pushctl\PushctlLaravel\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Pushctl\PushctlLaravel\PushctlServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            PushctlServiceProvider::class,
        ];
    }
}
