<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Employee;

use App\Module\Company\Domain\Aggregate\Employee\EmployeeAggregate;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\EmployeeUUID;

interface EmployeeAggregateReaderInterface
{
    public function getEmployeeAggregateByUUID(EmployeeUUID $uuid): EmployeeAggregate;
}
