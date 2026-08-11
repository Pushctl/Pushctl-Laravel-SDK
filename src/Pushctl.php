<?php

declare(strict_types=1);

namespace Flowgistics\Pushctl;

use Flowgistics\Pushctl\Messages\PushctlMessage;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use InvalidArgumentException;
use LogicException;

final readonly class Pushctl
{
    public function __construct(
        private Factory $http,
        private string $url,
        private string $token,
        private int $timeout = 10,
    ) {}

    /**
     * @param  string|array<mixed>  $recipients
     */
    public function send(PushctlMessage $message, string|array $recipients): Response
    {
        $response = $this->request($message)
            ->post('/api/v1/notifications', $message->toArray($this->recipients($recipients)));

        return $response->throw();
    }

    private function request(PushctlMessage $message): PendingRequest
    {
        if ($this->url === '') {
            throw new LogicException('Pushctl is not configured. Set PUSHCTL_URL in your environment.');
        }

        if ($this->token === '') {
            throw new LogicException('Pushctl is not configured. Set PUSHCTL_TOKEN in your environment.');
        }

        $request = $this->http
            ->baseUrl(rtrim($this->url, '/'))
            ->acceptJson()
            ->asJson()
            ->withToken($this->token)
            ->timeout($this->timeout);

        if ($message->idempotencyKey !== null) {
            $request->withHeader('Idempotency-Key', $message->idempotencyKey);
        }

        return $request;
    }

    /**
     * @param  string|array<mixed>  $recipients
     * @return non-empty-list<string>
     */
    private function recipients(string|array $recipients): array
    {
        $recipients = is_string($recipients) ? [$recipients] : $recipients;
        $recipients = array_values(array_unique(array_filter(
            $recipients,
            static fn (mixed $recipient): bool => is_string($recipient) && $recipient !== '',
        )));

        if ($recipients === []) {
            throw new InvalidArgumentException('At least one Pushctl recipient is required.');
        }

        return $recipients;
    }
}
