<?php

declare(strict_types=1);

namespace Flowgistics\Pushctl\Facades;

use Flowgistics\Pushctl\Messages\PushctlMessage;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Response send(PushctlMessage $message, string|array<mixed> $recipients)
 *
 * @see \Flowgistics\Pushctl\Pushctl
 */
class Pushctl extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Flowgistics\Pushctl\Pushctl::class;
    }
}
