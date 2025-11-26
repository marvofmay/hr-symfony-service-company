<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Department;

use App\Module\Company\Domain\Entity\Department;
use Doctrine\Common\Collections\Collection;

interface DepartmentWriterInterface
{
    public function saveDepartmentInDB(Department $department): void;

    public function saveDepartmentsInDB(Collection $departments): void;

    public function deleteDepartmentInDB(Department $department): void;

    public function deleteMultipleDepartmentsInDB(Collection $departments): void;
}
