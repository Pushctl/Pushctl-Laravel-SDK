<?php

declare(strict_types=1);

namespace Pushctl\PushctlLaravel\Facades;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Facade;
use Pushctl\PushctlLaravel\Messages\PushctlMessage;

/**
 * @method static Response send(PushctlMessage $message, string|array<mixed> $recipients)
 *
 * @see \Pushctl\PushctlLaravel\Pushctl
 */
class Pushctl extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Pushctl\PushctlLaravel\Pushctl::class;
    }
}
