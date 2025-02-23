<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Employee;

use App\Module\Company\Domain\Entity\Employee;

interface EmployeeWriterInterface
{
    public function saveEmployeeInDB(Employee $employee): void;
    public function updateEmployeeInDB(Employee $employee): void;
    public function saveEmployeesInDB(array $employees): void;
    public function deleteMultipleEmployeesInDB(array $selectedUUID): void;
}
