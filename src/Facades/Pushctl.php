<?php

declare(strict_types=1);

namespace Flowgistics\Pushctl\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Flowgistics\Pushctl\Pushctl
 */
class Pushctl extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Flowgistics\Pushctl\Pushctl::class;
    }
}
