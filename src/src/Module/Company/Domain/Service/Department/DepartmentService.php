<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Department;

use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Interface\Department\DepartmentWriterInterface;

readonly class DepartmentService
{
    public function __construct(private DepartmentWriterInterface $departmentWriterRepository)
    {
    }

    public function __toString()
    {
        return 'DepartmentService';
    }

    public function saveDepartmentInDB(Department $department): void
    {
        $this->departmentWriterRepository->saveDepartmentInDB($department);
    }

    public function updateDepartmentInDB(Department $department): void
    {
        $this->departmentWriterRepository->updateDepartmentInDB($department);
    }

    public function saveDepartmentsInDB(array $departments): void
    {
        $this->departmentWriterRepository->saveDepartmentsInDB($departments);
    }

    public function deleteMultipleDepartmentsInDB(array $selectedUUID): void
    {
        $this->departmentWriterRepository->deleteMultipleDepartmentsInDB($selectedUUID);
    }
}
