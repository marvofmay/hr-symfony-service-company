<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Interface\Employee\EmployeeWriterInterface;

readonly class EmployeeService
{
    public function __construct(private EmployeeWriterInterface $employeeWriterRepository)
    {
    }

    public function __toString()
    {
        return 'EmployeeService';
    }

    public function saveEmployeeInDB(Employee $employee): void
    {
        $this->employeeWriterRepository->saveEmployeeInDB($employee);
    }

    public function updateEmployeeInDB(Employee $employee): void
    {
        $this->employeeWriterRepository->updateEmployeeInDB($employee);
    }

    public function saveEmployeesInDB(array $employees): void
    {
        $this->employeeWriterRepository->saveEmployeesInDB($employees);
    }

    public function deleteMultipleEmployeesInDB(array $selectedUUID): void
    {
        $this->employeeWriterRepository->deleteMultipleEmployeesInDB($selectedUUID);
    }
}
