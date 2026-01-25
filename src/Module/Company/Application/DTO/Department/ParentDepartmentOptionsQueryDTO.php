<?php

declare(strict_types=1);

namespace App\Module\Company\Application\DTO\Department;

use App\Common\Domain\Interface\QueryDTOInterface;

class ParentDepartmentOptionsQueryDTO implements QueryDTOInterface
{
    public string $companyUUID;

    public ?string $departmentUUID = null;
}
