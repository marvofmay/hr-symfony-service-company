<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Query\Employee;

use App\Common\Domain\Interface\QueryDTOInterface;
use App\Common\Domain\Interface\QueryInterface;

final class GetAvailableParentEmployeeOptionsQuery implements QueryInterface
{
    public string $companyUUID;
    public ?string $departmentUUID = null;
    public ?string $employeeUUID = null;

    public function __construct(QueryDTOInterface $queryDTO)
    {
        $this->companyUUID = $queryDTO->companyUUID;
        $this->departmentUUID = $queryDTO->departmentUUID;
        $this->employeeUUID = $queryDTO->employeeUUID;
    }
}
