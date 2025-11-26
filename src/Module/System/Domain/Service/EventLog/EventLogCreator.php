<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Service\EventLog;

use App\Module\System\Domain\Entity\EventLog;
use App\Module\System\Domain\Interface\EventLog\EventLogCreatorInterface;
use App\Module\System\Domain\Interface\EventLog\EventLogWriterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class EventLogCreator implements EventLogCreatorInterface
{
    public function __construct(private EventLogWriterInterface $eventLogWriterRepository)
    {
    }

    public function create(string $eventClass, string $entityClass, string $jsonData, ?UserInterface $user): void
    {
        $eventLog = EventLog::create($eventClass, $entityClass, $jsonData, $user);
        $this->eventLogWriterRepository->saveEventLog($eventLog);
    }
}
