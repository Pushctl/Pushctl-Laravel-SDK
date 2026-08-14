<?php

declare(strict_types=1);

use Pushctl\PushctlLaravel\Messages\PushctlMessage;

it('builds the complete Pushctl API payload', function () {
    $message = PushctlMessage::create()
        ->title('Order ready')
        ->body('Your order is ready.')
        ->imageUrl('https://example.com/order.png')
        ->actionUrl('myapp://orders/8392')
        ->data(['order_id' => 8392])
        ->with('source', 'checkout')
        ->ttl(3600)
        ->priority('normal')
        ->collapseId('order-8392')
        ->sound('default')
        ->badge(3)
        ->androidChannelId('orders')
        ->iosCategory('ORDER_READY')
        ->ios(['interruption-level' => 'time-sensitive'])
        ->android(['notification' => ['color' => '#ff0000']]);

    expect($message->toArray(['user-1']))->toBe([
        'recipients' => ['user_ids' => ['user-1']],
        'notification' => [
            'title' => 'Order ready',
            'body' => 'Your order is ready.',
            'image_url' => 'https://example.com/order.png',
            'action_url' => 'myapp://orders/8392',
        ],
        'data' => [
            'order_id' => 8392,
            'source' => 'checkout',
        ],
        'options' => [
            'ttl' => 3600,
            'priority' => 'normal',
            'collapse_id' => 'order-8392',
            'sound' => 'default',
            'badge' => 3,
            'android_channel_id' => 'orders',
            'ios_category' => 'ORDER_READY',
            'platform_overrides' => [
                'ios' => ['interruption-level' => 'time-sensitive'],
                'android' => ['notification' => ['color' => '#ff0000']],
            ],
        ],
    ]);
});

it('rejects invalid message options', function (Closure $callback, string $message) {
    expect($callback)->toThrow(InvalidArgumentException::class, $message);
})->with([
    'negative ttl' => [fn (): PushctlMessage => PushctlMessage::create()->ttl(-1), 'Pushctl TTL'],
    'oversized ttl' => [fn (): PushctlMessage => PushctlMessage::create()->ttl(2_419_201), 'Pushctl TTL'],
    'unknown priority' => [fn (): PushctlMessage => PushctlMessage::create()->priority('urgent'), 'Pushctl priority'],
    'negative badge' => [fn (): PushctlMessage => PushctlMessage::create()->badge(-1), 'Pushctl badge'],
]);
