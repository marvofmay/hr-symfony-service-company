<?php

declare(strict_types=1);

namespace App\Common\Domain\Service\EventStore;

use App\Common\Domain\Entity\EventStore;
use App\Common\Domain\Interface\EventStoreWriterInterface;

class EventStoreCreator
{
    public function __construct(protected EventStoreWriterInterface $eventStoreWriterRepository)
    {
    }

    public function create(EventStore $eventStore): void
    {
        $this->eventStoreWriterRepository->saveEventStoreInDB($eventStore);
    }
}
