<?php

namespace App\Module\System\Application\EventSubscriber;

use App\Common\Domain\Interface\NotifiableEventInterface;
use App\Module\System\Notification\Domain\Interface\Message\NotificationResolveInterface;

final readonly class GenericEventNotifierSubscriber
{
    public function __construct(private NotificationResolveInterface $notificationResolve)
    {
    }

    public function onNotifiableEvent(NotifiableEventInterface $event): void
    {
        $this->notificationResolve->resolve($event);
    }
}
