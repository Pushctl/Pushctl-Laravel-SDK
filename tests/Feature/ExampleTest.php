<?php

declare(strict_types=1);

use Flowgistics\Pushctl\Channels\PushctlChannel;
use Flowgistics\Pushctl\Messages\PushctlMessage;
use Flowgistics\Pushctl\Pushctl;
use Illuminate\Http\Client\Request;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification as NotificationFacade;

it('resolves the singleton', function () {
    expect(app(Pushctl::class))->toBeInstanceOf(Pushctl::class);
});

it('returns the same instance from the container', function () {
    expect(app(Pushctl::class))->toBe(app(Pushctl::class));
});

it('merges the package config', function () {
    expect(config('pushctl-laravel-sdk.timeout'))->toBe(10);
});

it('sends Laravel notifications through Pushctl', function () {
    config()->set('pushctl-laravel-sdk.url', 'https://pushctl.example');
    config()->set('pushctl-laravel-sdk.token', 'secret-token');

    Http::fake([
        'pushctl.example/*' => Http::response([
            'data' => ['id' => 'notification-1', 'status' => 'queued'],
            'meta' => ['accepted' => true],
        ], 202),
    ]);

    NotificationFacade::route('pushctl', ['00015', 'user-2'])
        ->notify(new class extends Notification
        {
            /** @return list<class-string> */
            public function via(object $notifiable): array
            {
                return [PushctlChannel::class];
            }

            public function toPushctl(object $notifiable): PushctlMessage
            {
                return PushctlMessage::create('Order ready', 'Your order is ready for collection.')
                    ->actionUrl('myapp://orders/8392')
                    ->imageUrl('https://example.com/order.png')
                    ->with('order_id', '8392')
                    ->ttl(3600)
                    ->priority('high')
                    ->collapseId('order-8392')
                    ->idempotencyKey('order-8392-ready');
            }
        });

    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://pushctl.example/api/v1/notifications'
            && $request->hasHeader('Authorization', 'Bearer secret-token')
            && $request->hasHeader('Idempotency-Key', 'order-8392-ready')
            && $request['recipients']['user_ids'] === ['00015', 'user-2']
            && $request['notification']['title'] === 'Order ready'
            && $request['notification']['body'] === 'Your order is ready for collection.'
            && $request['notification']['image_url'] === 'https://example.com/order.png'
            && $request['notification']['action_url'] === 'myapp://orders/8392'
            && $request['data']['order_id'] === '8392'
            && $request['options']['ttl'] === 3600
            && $request['options']['priority'] === 'high'
            && $request['options']['collapse_id'] === 'order-8392';
    });
});

it('does not send when the notifiable has no Pushctl route', function () {
    Http::fake();

    $notifiable = (new AnonymousNotifiable)->route('pushctl', null);
    $notification = new class extends Notification
    {
        public function toPushctl(object $notifiable): PushctlMessage
        {
            return PushctlMessage::create('Hello');
        }
    };

    app(PushctlChannel::class)->send($notifiable, $notification);

    Http::assertNothingSent();
});
