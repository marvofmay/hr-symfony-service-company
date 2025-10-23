<?php

namespace App\Module\Company\Application\Command\Employee;

use App\Common\Domain\Interface\CommandInterface;

final readonly class DeleteEmployeeCommand implements CommandInterface
{
    public function __construct(public string $employeeUUID)
    {
    }
}
