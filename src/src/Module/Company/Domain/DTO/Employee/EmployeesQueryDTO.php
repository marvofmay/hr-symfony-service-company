<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Employee;

use App\Common\Domain\Abstract\QueryDTOAbstract;

class EmployeesQueryDTO extends QueryDTOAbstract
{
    public ?string $firstName = null;

    public ?string $lastName = null;

    public ?bool $active = null;
}
