<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Service\EventLog;

use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use App\Module\System\Domain\Entity\EventLog;
use App\Module\System\Domain\Interface\EventLog\EventLogCreatorInterface;
use App\Module\System\Domain\Interface\EventLog\EventLogWriterInterface;

final readonly class EventLogCreator implements EventLogCreatorInterface
{
    public function __construct(private EventLogWriterInterface $eventLogWriterRepository)
    {
    }

    public function create(string $eventClass, string $entityClass, string $jsonData, ?Employee $employee): void
    {
        $this->eventLogWriterRepository->saveEventLogInDB(new EventLog($eventClass, $entityClass, $jsonData, $employee));
    }
}
