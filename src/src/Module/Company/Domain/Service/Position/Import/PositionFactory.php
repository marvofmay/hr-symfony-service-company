<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position\Import;

use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Enum\Position\PositionImportColumnEnum;

final class PositionFactory
{
    public function create(array $positionData): Position
    {
        $position = new Position();
        $this->fillData($position, $positionData);

        return $position;
    }

    public function update(array $positionData, array $existingPositions): Position
    {
        $position = $existingPositions[$positionData[PositionImportColumnEnum::POSITION_NAME->value]];
        $this->fillData($position, $positionData);

        return $position;
    }

    private function fillData(Position $position, array $positionData): void
    {
        $name = $positionData[PositionImportColumnEnum::POSITION_NAME->value] ?? null;
        $description = $positionData[PositionImportColumnEnum::POSITION_DESCRIPTION->value] ?? null;
        $active = $positionData[PositionImportColumnEnum::POSITION_ACTIVE->value] ?? false;

        $position->setName($name);
        $position->setDescription($description);
        $position->setActive($active);
    }
}
