<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Employee;

use App\Module\Company\Domain\Entity\Employee;
use App\Module\System\Application\Event\Event;

class EmployeeEvent extends Event
{
    public function getEntityClass(): string
    {
        return Employee::class;
    }
}
