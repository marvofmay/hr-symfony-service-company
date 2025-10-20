<?php

namespace App\Module\System\Application\EventSubscriber;

use App\Module\System\Domain\Abstract\EventLog\AbstractEventLoggerSubscriber;
use App\Module\System\Domain\Interface\EventLog\LoggableEventInterface;
use App\Module\System\Domain\Provider\LoggableEventsProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class GenericEventLoggerSubscriber extends AbstractEventLoggerSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        // ToDo:: refactor - use tags - OCP SOLID
        return LoggableEventsProvider::getEvents();
    }

    public function onLoggableEvent(LoggableEventInterface $event): void
    {
        $this->log($event::class, $event->getEntityClass(), $event->getData());
    }
}
