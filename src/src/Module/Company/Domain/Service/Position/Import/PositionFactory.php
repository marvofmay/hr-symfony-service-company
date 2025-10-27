<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position\Import;

use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Enum\Position\PositionImportColumnEnum;

final class PositionFactory
{
    public function createOrUpdatePosition(?string $uuid, array $existingPositions, array $positionData): Position
    {
        $position = null === $uuid
            ? new Position()
            : $existingPositions[$positionData[PositionImportColumnEnum::POSITION_NAME->value]];

        $position->setName($positionData[PositionImportColumnEnum::POSITION_NAME->value]);
        $position->setDescription($positionData[PositionImportColumnEnum::POSITION_DESCRIPTION->value]);
        $position->setActive((bool) $positionData[PositionImportColumnEnum::ACTIVE->value]);

        return $position;
    }
}
