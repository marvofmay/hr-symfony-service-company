<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position;

use App\Module\Company\Application\Command\Position\UpdatePositionCommand;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;

final readonly class PositionDepartmentUpdater
{
    public function __construct(
        private DepartmentReaderInterface $departmentReaderRepository,
        private DepartmentSynchronizer $departmentSynchronizer,
    ) {
    }

    public function updateDepartments(Position $position, UpdatePositionCommand $command): void
    {
        $departments = $this->departmentReaderRepository->getDepartments()->toArray();

        $existingDepartments = [];
        $payloadInternalCodes = [];

        foreach ($departments as $department) {
            $existingDepartments[$department->getInternalCode()] = $department;
        }

        foreach ($existingDepartments as $existingDepartment) {
            if (in_array($existingDepartment->getUUID()->toString(), $command->departmentsUUIDs, true)) {
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
