<?php

declare(strict_types=1);

namespace App\Common\Domain\Interface;

use App\Common\Domain\Entity\EventStore;

interface EventStoreWriterInterface
{
    public function saveEventStoreInDB(EventStore $eventStore): void;
}