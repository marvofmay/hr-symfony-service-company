<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Employee;


use App\Module\Company\Domain\Entity\Employee;

final readonly class RestoreEmployeeCommand
{
    public function __construct(private Employee $employee,)
    {
    }

    public function getEmployee(): Employee
    {
        return $this->employee;
    }
}
