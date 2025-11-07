<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Department;

use App\Module\Company\Domain\Entity\Department;
use App\Module\System\Application\Event\Event;

class DepartmentEvent extends Event
{
    public function getEntityClass(): string
    {
        return Department::class;
    }
}
