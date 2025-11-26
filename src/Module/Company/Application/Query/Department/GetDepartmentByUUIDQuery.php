<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Query\Department;

use App\Common\Domain\Interface\QueryInterface;

final class GetDepartmentByUUIDQuery implements QueryInterface
{
    public function __construct(public string $departmentUUID)
    {
    }
}
