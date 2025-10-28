<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position\Import;

use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Enum\Position\PositionImportColumnEnum;

final class PositionFactory
{
    public function createOrUpdatePosition(array $existingPositions, array $positionData): Position
    {
        $position = array_key_exists($positionData[PositionImportColumnEnum::POSITION_NAME->value], $existingPositions)
            ? $existingPositions[$positionData[PositionImportColumnEnum::POSITION_NAME->value]]
            : new Position();

        $position->setName($positionData[PositionImportColumnEnum::POSITION_NAME->value]);
        $position->setDescription($positionData[PositionImportColumnEnum::POSITION_DESCRIPTION->value]);
        $position->setActive((bool) $positionData[PositionImportColumnEnum::ACTIVE->value]);

        return $position;
    }
}
