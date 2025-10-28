<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position\Import;

use App\Module\Company\Domain\Enum\Position\PositionImportColumnEnum;
use App\Module\Company\Domain\Interface\Position\PositionWriterInterface;
use App\Module\Company\Domain\Service\Position\DepartmentSynchronizer;
use Doctrine\Common\Collections\ArrayCollection;

final class PositionsImporter
{
    private array $positions = [];

    public function __construct(
        private readonly PositionWriterInterface $positionWriter,
        private readonly PositionFactory $positionFactory,
        private readonly DepartmentSynchronizer $departmentSynchronizer,
    ) {
    }

    public function save(array $positionNameMap, array $groupPositions, array $existingPositions, array $existingDepartments): void
    {
        foreach ($positionNameMap as $name => $uuid) {
            $position = $this->positionFactory->createOrUpdatePosition($existingPositions, $groupPositions[$name]);

            $this->departmentSynchronizer->syncDepartments(
                $position,
                $groupPositions[$name][PositionImportColumnEnum::DEPARTMENT_INTERNAL_CODE->value],
                $existingDepartments
            );

            $this->positions[] = $position;
        }

        $this->positionWriter->savePositionsInDB(new ArrayCollection($this->positions));
    }
}
