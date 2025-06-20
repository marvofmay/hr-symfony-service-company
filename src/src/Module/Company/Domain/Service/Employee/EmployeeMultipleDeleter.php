<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Module\Company\Domain\Interface\Employee\EmployeeWriterInterface;
use Doctrine\Common\Collections\Collection;

readonly class EmployeeMultipleDeleter
{
    public function __construct(private EmployeeWriterInterface $employeeWriterRepository)
    {
    }

    public function multipleDelete(Collection $employees): void
    {
        $this->employeeWriterRepository->deleteMultipleEmployeesInDB($employees);
    }
}
