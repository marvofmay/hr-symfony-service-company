<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position;

use App\Common\Domain\Enum\DeleteTypeEnum;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Interface\PositionDepartment\PositionDepartmentWriterInterface;

final readonly class DepartmentSynchronizer
{
    public function __construct(
        private PositionDepartmentWriterInterface $positionDepartmentWriter,
    ) {
    }

    public function syncDepartments(Position $position, array $payloadInternalCodes, array $existingDepartments): void
    {
        $remainingCodes = $payloadInternalCodes;

        foreach ($position->getDepartments() as $currentDepartment) {
            if (in_array($currentDepartment->getInternalCode(), $remainingCodes, true)) {
                $remainingCodes = array_values(array_filter(
                    $remainingCodes,
                    fn ($code) => $code !== $currentDepartment->getInternalCode()
                ));
                continue;
            }

            $position->removeDepartment($currentDepartment);
            $this->positionDepartmentWriter->deletePositionDepartmentByPositionInDB(
                $position,
                $currentDepartment,
                DeleteTypeEnum::HARD_DELETE
            );
        }

        $departmentsToAdd = array_intersect_key(
            $existingDepartments,
            array_flip($remainingCodes)
        );

        foreach ($departmentsToAdd as $department) {
            $position->addDepartment($department);
        }
    }
}
