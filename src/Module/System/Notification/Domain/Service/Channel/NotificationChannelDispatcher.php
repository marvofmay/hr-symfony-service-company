<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Service\Channel;

use App\Module\System\Notification\Domain\Entity\NotificationChannelSetting;
use App\Module\System\Notification\Domain\Entity\NotificationEventSetting;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class NotificationChannelDispatcher
{
    public function __construct(#[AutowireIterator(tag: 'app.notification.channel.handler')] private iterable $handlers)
    {
    }

    public function dispatch(NotificationEventSetting $event, NotificationChannelSetting $channel, array $recipientUUIDs, string $title, string $content, array $payload): void
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($channel)) {
                $handler->send($event, $recipientUUIDs, $title, $content, $payload);

                return;
            }
        }

        throw new \RuntimeException(sprintf('No handler found for channel: %s', $channel->getChannelCode()));
    }
}
