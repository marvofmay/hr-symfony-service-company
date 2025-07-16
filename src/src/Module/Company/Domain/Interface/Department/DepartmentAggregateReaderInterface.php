<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Department;

use App\Module\Company\Domain\Aggregate\Department\DepartmentAggregate;
use App\Module\Company\Domain\Aggregate\Department\ValueObject\DepartmentUUID;

interface DepartmentAggregateReaderInterface
{
    public function getDepartmentAggregateByUUID(DepartmentUUID $uuid): DepartmentAggregate;
}
