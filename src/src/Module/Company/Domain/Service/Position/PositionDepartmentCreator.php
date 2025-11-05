<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position;

use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Position\PositionDepartmentCreatorInterface;

final readonly class PositionDepartmentCreator implements PositionDepartmentCreatorInterface
{
    public function __construct(private DepartmentReaderInterface $departmentReaderRepository)
    {
    }

    public function createDepartments(Position $position, array $departmentsUUIDs = []): void
    {
        $departments = $this->departmentReaderRepository->getDepartmentsByUUID($departmentsUUIDs);
        foreach ($departments as $department) {
            $position->addDepartment($department);
        }
    }
}
