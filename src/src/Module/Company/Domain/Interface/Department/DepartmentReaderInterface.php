<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Department;

use App\Module\Company\Domain\Entity\Department;
use Doctrine\Common\Collections\Collection;

interface DepartmentReaderInterface
{
    public function getDepartmentByUUID(string $uuid): ?Department;

    public function getDepartmentsByUUID(array $selectedUUID): Collection;

    public function getDepartmentByName(string $name, ?string $uuid): ?Department;

    public function isDepartmentExistsWithName(string $name, ?string $departmentUUID = null): bool;

    public function isDepartmentExistsWithUUID(string $departmentUUID): bool;
}
