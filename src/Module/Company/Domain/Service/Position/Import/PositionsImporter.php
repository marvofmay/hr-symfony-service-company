<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position\Import;

use App\Module\Company\Domain\Enum\Position\PositionImportColumnEnum;
use App\Module\Company\Domain\Interface\Position\Import\PositionsImporterInterface;
use App\Module\Company\Domain\Interface\Position\PositionWriterInterface;
use App\Module\Company\Domain\Service\Position\DepartmentSynchronizer;
use Doctrine\Common\Collections\ArrayCollection;

final class PositionsImporter implements PositionsImporterInterface
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
            if (null !== $uuid) {
                $position = $existingPositions[$name];
                $position = $this->positionFactory->update(position: $position, data: $groupPositions[$name]);
            } else {
                $position = $this->positionFactory->create(data: $groupPositions[$name]);
            }

            $this->departmentSynchronizer->syncDepartments(
                position: $position,
                internalCodes: $groupPositions[$name][PositionImportColumnEnum::DEPARTMENT_INTERNAL_CODE->value],
                existingDepartments: $existingDepartments
            );

            $this->positions[] = $position;
        }

        $this->positionWriter->savePositionsInDB(new ArrayCollection($this->positions));
    }
}
