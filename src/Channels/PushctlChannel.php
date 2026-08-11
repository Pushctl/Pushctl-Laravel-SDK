<?php

declare(strict_types=1);

namespace Flowgistics\Pushctl\Channels;

use Flowgistics\Pushctl\Messages\PushctlMessage;
use Flowgistics\Pushctl\Pushctl;
use Illuminate\Http\Client\Response;
use Illuminate\Notifications\Notification;
use LogicException;

final readonly class PushctlChannel
{
    public function __construct(private Pushctl $pushctl) {}

    public function send(object $notifiable, Notification $notification): ?Response
    {
        if (! method_exists($notification, 'toPushctl')) {
            throw new LogicException(sprintf(
                'Notification [%s] must define a toPushctl method.',
                $notification::class,
            ));
        }

        $message = $notification->toPushctl($notifiable);

        if (! $message instanceof PushctlMessage) {
            throw new LogicException(sprintf(
                'Notification [%s] must return a PushctlMessage from toPushctl.',
                $notification::class,
            ));
        }

        if (! method_exists($notifiable, 'routeNotificationFor')) {
            throw new LogicException(sprintf(
                'Notifiable [%s] must define a routeNotificationFor method.',
                $notifiable::class,
            ));
        }

        $recipients = $notifiable->routeNotificationFor('pushctl', $notification);

        if ($recipients === null || $recipients === '' || $recipients === []) {
            return null;
        }

        if (! is_string($recipients) && ! is_array($recipients)) {
            throw new LogicException('The Pushctl notification route must be a user ID or an array of user IDs.');
        }

        return $this->pushctl->send($message, $recipients);
    }
}
