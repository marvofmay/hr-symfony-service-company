<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Department;

use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Interface\Department\DepartmentWriterInterface;

readonly class DepartmentDeleter
{
    public function __construct(private DepartmentWriterInterface $departmentWriterRepository)
    {
    }

    public function delete(Department $department): void
    {
        $this->departmentWriterRepository->deleteDepartmentInDB($department);
    }
}
