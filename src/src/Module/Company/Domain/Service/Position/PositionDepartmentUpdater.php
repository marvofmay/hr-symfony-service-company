<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position;

use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Position\PositionDepartmentUpdaterInterface;

final readonly class PositionDepartmentUpdater implements PositionDepartmentUpdaterInterface
{
    public function __construct(
        private DepartmentReaderInterface $departmentReaderRepository,
        private DepartmentSynchronizer $departmentSynchronizer,
    ) {
    }

    public function updateDepartments(Position $position, array $departmentsUUIDs = []): void
    {
        $departments = $this->departmentReaderRepository->getDepartments()->toArray();

        $existingDepartments = [];
        $payloadInternalCodes = [];

        foreach ($departments as $department) {
            $existingDepartments[$department->getInternalCode()] = $department;
        }

        foreach ($existingDepartments as $existingDepartment) {
            if (in_array($existingDepartment->getUUID()->toString(), $departmentsUUIDs, true)) {
                $payloadInternalCodes[] = $existingDepartment->getInternalCode();
            }
        }

        $this->departmentSynchronizer->syncDepartments(
            $position,
            $payloadInternalCodes,
            $existingDepartments
        );
    }
}
