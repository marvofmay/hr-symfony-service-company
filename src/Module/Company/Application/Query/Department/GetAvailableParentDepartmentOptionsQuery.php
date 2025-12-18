<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Query\Department;

use App\Common\Domain\Interface\QueryDTOInterface;
use App\Common\Domain\Interface\QueryInterface;

final class GetAvailableParentDepartmentOptionsQuery implements QueryInterface
{
    public string $companyUUID;
    public ?string $departmentUUID = null;

    public function __construct(QueryDTOInterface $queryDTO)
    {
        $this->companyUUID = $queryDTO->companyUUID;
        $this->departmentUUID = $queryDTO->departmentUUID;
    }
}
