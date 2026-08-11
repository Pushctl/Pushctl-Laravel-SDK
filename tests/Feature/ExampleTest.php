<?php

declare(strict_types=1);

use Flowgistics\Pushctl\Pushctl;

it('resolves the singleton', function () {
    expect(app(Pushctl::class))->toBeInstanceOf(Pushctl::class);
});

it('returns the same instance from the container', function () {
    expect(app(Pushctl::class))->toBe(app(Pushctl::class));
});

it('merges the package config', function () {
    expect(config('pushctl-laravel-sdk.placeholder'))->toBe('default');
});
