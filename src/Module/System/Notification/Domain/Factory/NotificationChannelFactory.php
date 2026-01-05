<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Factory;

use App\Module\System\Notification\Domain\Interface\Channel\NotificationChannelInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class NotificationChannelFactory
{
    public function __construct(#[AutowireIterator(tag: 'app.notification.channel')] private iterable $channels)
    {
    }

    public function getChannel(string $code): ?NotificationChannelInterface
    {
        foreach ($this->channels as $channel) {
            if ($channel->getCode() === $code) {
                return $channel;
            }
        }

        return null;
    }

    public function all(): array
    {
        return iterator_to_array($this->channels);
    }
}
