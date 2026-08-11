<?php

declare(strict_types=1);

namespace Flowgistics\Pushctl\Messages;

use InvalidArgumentException;

final class PushctlMessage
{
    /** @var array<string, mixed> */
    private array $data = [];

    /** @var array<string, mixed> */
    private array $options = [];

    public ?string $idempotencyKey = null;

    private function __construct(
        private ?string $title = null,
        private ?string $body = null,
        private ?string $imageUrl = null,
        private ?string $actionUrl = null,
    ) {}

    public static function create(?string $title = null, ?string $body = null): self
    {
        return new self($title, $body);
    }

    public function title(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function body(?string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function imageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function actionUrl(?string $actionUrl): self
    {
        $this->actionUrl = $actionUrl;

        return $this;
    }

    /** @param array<string, mixed> $data */
    public function data(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function with(string $key, mixed $value): self
    {
        $this->data[$key] = $value;

        return $this;
    }

    public function ttl(int $seconds): self
    {
        if ($seconds < 0 || $seconds > 2_419_200) {
            throw new InvalidArgumentException('Pushctl TTL must be between 0 and 2419200 seconds.');
        }

        return $this->option('ttl', $seconds);
    }

    public function priority(string $priority): self
    {
        if (! in_array($priority, ['normal', 'high'], true)) {
            throw new InvalidArgumentException('Pushctl priority must be normal or high.');
        }

        return $this->option('priority', $priority);
    }

    public function collapseId(string $collapseId): self
    {
        return $this->option('collapse_id', $collapseId);
    }

    public function sound(string $sound): self
    {
        return $this->option('sound', $sound);
    }

    public function badge(int $badge): self
    {
        if ($badge < 0) {
            throw new InvalidArgumentException('Pushctl badge must be zero or greater.');
        }

        return $this->option('badge', $badge);
    }

    public function androidChannelId(string $channelId): self
    {
        return $this->option('android_channel_id', $channelId);
    }

    public function iosCategory(string $category): self
    {
        return $this->option('ios_category', $category);
    }

    /** @param array<string, mixed> $overrides */
    public function ios(array $overrides): self
    {
        $this->options['platform_overrides']['ios'] = $overrides;

        return $this;
    }

    /** @param array<string, mixed> $overrides */
    public function android(array $overrides): self
    {
        $this->options['platform_overrides']['android'] = $overrides;

        return $this;
    }

    public function idempotencyKey(?string $idempotencyKey): self
    {
        $this->idempotencyKey = $idempotencyKey;

        return $this;
    }

    /**
     * @param  non-empty-list<string>  $recipients
     * @return array{
     *     recipients: array{user_ids: non-empty-list<string>},
     *     notification: array{title: string|null, body: string|null, image_url: string|null, action_url: string|null},
     *     data: array<string, mixed>,
     *     options: array<string, mixed>
     * }
     */
    public function toArray(array $recipients): array
    {
        return [
            'recipients' => ['user_ids' => $recipients],
            'notification' => [
                'title' => $this->title,
                'body' => $this->body,
                'image_url' => $this->imageUrl,
                'action_url' => $this->actionUrl,
            ],
            'data' => $this->data,
            'options' => $this->options,
        ];
    }

    private function option(string $key, mixed $value): self
    {
        $this->options[$key] = $value;

        return $this;
    }
}
