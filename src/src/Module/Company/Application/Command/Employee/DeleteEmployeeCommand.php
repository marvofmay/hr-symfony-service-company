<?php

namespace App\Module\Company\Application\Command\Employee;

use App\Module\Company\Domain\Entity\Employee;

readonly class DeleteEmployeeCommand
{
    public function __construct(private Employee $employee)
    {
    }

    public function getEmployee(): Employee
    {
        return $this->employee;
    }
}
