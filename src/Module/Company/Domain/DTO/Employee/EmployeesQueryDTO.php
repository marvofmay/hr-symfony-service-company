<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Employee;

use App\Common\Domain\Abstract\QueryDTOAbstract;

class EmployeesQueryDTO extends QueryDTOAbstract
{
    public ?string $firstName = null;

    public ?string $lastName = null;

    public ?string $pesel = null;

    public ?string $internalCode = null;
    public ?string $externalCode = null;
    public ?string $companyUUID = null;
    public ?string $departmentUUID = null;
    public ?string $roleUUID = null;
    public ?string $positionUUID = null;
    public ?string $contractTypeUUID = null;

    public ?bool $active = null;
}
