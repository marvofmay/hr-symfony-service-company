<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\EventLog;

use App\Module\System\Domain\Entity\EventLog;

interface EventLogWriterInterface
{
    public function saveEventLogInDB(EventLog $eventLog): void;
}
