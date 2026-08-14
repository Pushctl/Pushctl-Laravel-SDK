---
name: pushctl-laravel-development
description: >
  Configure and apply the Pushctl Laravel Sdk package in Laravel applications.
license: MIT
metadata:
  author: Pushctl
---

# Pushctl Laravel Sdk

Use this skill when a Laravel application needs to integrate the Pushctl Laravel Sdk package.

## Primary Goal

- apply the `pushctl/pushctl-laravel` package's public API in the smallest correct way

## Workflow

### 1. Inspect the Laravel app context

- confirm the app is a Laravel project
- inspect the target code paths where the package should be applied

### 2. Apply the package's public API

- configure `PUSHCTL_URL` and `PUSHCTL_TOKEN`
- return `Pushctl\PushctlLaravel\Channels\PushctlChannel::class` from the notification's `via` method
- return a `Pushctl\PushctlLaravel\Messages\PushctlMessage` from `toPushctl`
- expose the external user ID from `routeNotificationForPushctl` on the notifiable
- use `Pushctl\PushctlLaravel\Pushctl::send` only when sending outside Laravel's notification system

## Rules, References, and Templates

Read before executing:

- no additional resource files for this skill

## Examples

```php
public function via(object $notifiable): array
{
    return [PushctlChannel::class];
}

public function toPushctl(object $notifiable): PushctlMessage
{
    return PushctlMessage::create('Order ready', 'Your order is ready.')
        ->actionUrl("myapp://orders/{$this->orderId}")
        ->with('order_id', $this->orderId)
        ->priority('high');
}
```

## Anti-patterns

- do not document package internals here; keep the skill focused on adoption in Laravel apps
- do not send provider device tokens; Pushctl routes notifications by the application's external user IDs
