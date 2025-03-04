<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Department;

use App\Module\Company\Domain\Entity\Department;

interface DepartmentWriterInterface
{
    public function saveDepartmentInDB(Department $department): void;
    public function updateDepartmentInDB(Department $department): void;
    public function saveDepartmentsInDB(array $departments): void;
    public function deleteDepartmentInDB(Department $department): void;
    public function deleteMultipleDepartmentsInDB(array $selectedUUID): void;
}
