<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Employee;

use App\Common\Domain\Interface\QueryDTOInterface;

class ParentEmployeeOptionsQueryDTO implements QueryDTOInterface
{
    public string $companyUUID;

    public ?string $departmentUUID = null;
    public ?string $employeeUUID = null;
}
