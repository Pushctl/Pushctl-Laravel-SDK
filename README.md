<div align="center">
    <h1>Pushctl Laravel Sdk</h1>
</div>

<p align="center">
    <a href="https://packagist.org/packages/pushctl/pushctl-laravel"><img src="https://img.shields.io/packagist/v/pushctl/pushctl-laravel.svg?style=flat-square" alt="Packagist"></a>
    <a href="https://packagist.org/packages/pushctl/pushctl-laravel"><img src="https://img.shields.io/packagist/php-v/pushctl/pushctl-laravel.svg?style=flat-square" alt="PHP from Packagist"></a>
    <a href="https://packagist.org/packages/pushctl/pushctl-laravel"><img src="https://badge.laravel.cloud/badge/pushctl/pushctl-laravel?style=flat" alt="Laravel versions"></a>
    <a href="https://github.com/Pushctl/Pushctl-Laravel-SDK/actions"><img alt="GitHub Workflow Status (master)" src="https://img.shields.io/github/actions/workflow/status/Pushctl/Pushctl-Laravel-SDK/tests.yml?branch=master&label=Tests&style=flat-square"></a>
    <a href="https://packagist.org/packages/pushctl/pushctl-laravel"><img src="https://img.shields.io/packagist/dt/pushctl/pushctl-laravel.svg?style=flat-square" alt="Total Downloads"></a>
</p>

Push notifications for developers

## Installation

You can install the package via Composer:

```bash
composer require pushctl/pushctl-laravel
```

You may publish all of the package's resources at once:

```bash
php artisan vendor:publish --tag="pushctl"
```

Or, you may publish each resource individually:

### Publishing the Configuration File

```bash
php artisan vendor:publish --tag="pushctl-config"
```

## Usage

Configure the Pushctl API URL and an application token with the
`notifications:send` ability:

```dotenv
PUSHCTL_TOKEN=your-application-token
```

Add the Pushctl channel to a Laravel notification and return a fluent message
from `toPushctl`:

```php
use Pushctl\PushctlLaravel\Channels\PushctlChannel;
use Pushctl\PushctlLaravel\Messages\PushctlMessage;
use Illuminate\Notifications\Notification;

final class OrderReady extends Notification
{
    public function __construct(private readonly Order $order) {}

    public function via(object $notifiable): array
    {
        return [PushctlChannel::class];
    }

    public function toPushctl(object $notifiable): PushctlMessage
    {
        return PushctlMessage::create('Order ready', 'Your order is ready for collection.')
            ->actionUrl("myapp://orders/{$this->order->id}")
            ->with('order_id', $this->order->id)
            ->ttl(3600)
            ->priority('high')
            ->collapseId("order-{$this->order->id}");
    }
}
```

Route a notifiable model to its external Pushctl user ID. An array may be
returned when one notification should target multiple external users.

```php
use Illuminate\Notifications\Notification;

public function routeNotificationForPushctl(Notification $notification): string|array
{
    return $this->pushctl_user_id;
}
```

Pushctl supports visible and data-only messages. The builder also provides
`imageUrl`, `data`, `sound`, `badge`, `androidChannelId`, `iosCategory`, `ios`,
`android`, and `idempotencyKey` methods.

Messages can also be sent directly:

```php
use Pushctl\PushctlLaravel\Pushctl;

app(Pushctl::class)->send(
    PushctlMessage::create('Hello')->with('context', 'welcome'),
    ['external-user-1', 'external-user-2'],
);
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Thank you for considering contributing to Pushctl Laravel Sdk! Please review our [contributing guide](.github/CONTRIBUTING.md) to get started.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Pushctl](https://github.com/pushctl)
- [Flowgistics](https://flowgistics.nl)
- [All Contributors](../../contributors)

## License

Pushctl Laravel Sdk is open-sourced software licensed under the [MIT license](LICENSE.md).
