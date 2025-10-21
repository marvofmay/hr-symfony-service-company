<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Department;

use App\Module\Company\Application\Event\Event;
use App\Module\Company\Domain\Entity\Department;

class DepartmentEvent extends Event
{
    public function getEntityClass(): string
    {
        return Department::class;
    }
}
