<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Department;

use App\Module\Company\Domain\Aggregate\Company\CompanyAggregate;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;

interface DepartmentAggregateReaderInterface
{
    public function getDepartmentAggregateByUUID(CompanyUUID $uuid): CompanyAggregate;
}
