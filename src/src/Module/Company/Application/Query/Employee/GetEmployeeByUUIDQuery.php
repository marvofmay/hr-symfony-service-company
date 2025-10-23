<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Query\Employee;

use App\Common\Domain\Interface\QueryInterface;

final class GetEmployeeByUUIDQuery implements QueryInterface
{
    public function __construct(public string $employeeUUID)
    {
    }
}
