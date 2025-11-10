<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Factory;

use App\Module\System\Notification\Domain\Interface\Event\NotificationEventInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class NotificationEventFactory
{
    public function __construct(#[AutowireIterator(tag: 'app.notification.event')] private iterable $events,) {}

    public function getEvent(string $code): ?NotificationEventInterface
    {
        foreach ($this->events as $event) {
            if ($event->getCode() === $code) {
                return $event;
            }
        }

        return null;
    }

    public function all(): array
    {
        return iterator_to_array($this->events);
    }
}