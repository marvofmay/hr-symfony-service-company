<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Department;

use App\Module\Company\Domain\Interface\Department\DepartmentWriterInterface;
use Doctrine\Common\Collections\Collection;

readonly class DepartmentMultipleDeleter
{
    public function __construct(private DepartmentWriterInterface $departmentWriterRepository)
    {
    }

    public function multipleDelete(Collection $departments): void
    {
        $this->departmentWriterRepository->deleteMultipleDepartmentsInDB($departments);
    }
}
