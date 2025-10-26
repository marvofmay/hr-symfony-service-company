<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\EventLog;

use App\Module\Company\Domain\Entity\Employee;

interface EventLogCreatorInterface
{
    public function create(string $eventClass, string $entityClass, string $jsonData, ?Employee $employee): void;
}
