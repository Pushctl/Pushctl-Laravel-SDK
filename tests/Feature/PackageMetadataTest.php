<?php

declare(strict_types=1);

use Pushctl\PushctlLaravel\Facades\Pushctl;
use Pushctl\PushctlLaravel\PushctlServiceProvider;

it('declares the Laravel package discovery metadata', function () {
    $composer = json_decode(
        (string) file_get_contents(dirname(__DIR__, 2).'/composer.json'),
        true,
        flags: JSON_THROW_ON_ERROR,
    );

    expect(data_get($composer, 'extra.laravel.providers'))
        ->toContain(PushctlServiceProvider::class)
        ->and(data_get($composer, 'extra.laravel.aliases.Pushctl'))
        ->toBe(Pushctl::class);
});
