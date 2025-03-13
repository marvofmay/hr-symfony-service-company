<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Interface\Employee\EmployeeWriterInterface;

readonly class EmployeeDeleter
{
    public function __construct(private EmployeeWriterInterface $employeeWriterRepository)
    {
    }

    public function delete(Employee $employee): void
    {
        $this->employeeWriterRepository->deleteEmployeeInDB($employee);
    }
}