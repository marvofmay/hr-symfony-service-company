<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position\Import;

use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Enum\Position\PositionImportColumnEnum;

final class PositionFactory
{
    public function create(array $data): Position
    {
        return Position::create(
            trim($data[PositionImportColumnEnum::POSITION_NAME->value]) ?? '',
            $data[PositionImportColumnEnum::POSITION_DESCRIPTION->value] ?? null,
            (bool)$data[PositionImportColumnEnum::POSITION_ACTIVE->value],
        );
    }

    public function update(Position $position, array $data): Position
    {
        $name = trim($data[PositionImportColumnEnum::POSITION_NAME->value]) ?? '';
        $description = $data[PositionImportColumnEnum::POSITION_DESCRIPTION->value] ?? null;
        $active = (bool)$data[PositionImportColumnEnum::POSITION_DESCRIPTION->value];
        
        $position->rename($name);
        $position->updateDescription($description);
        if ($active) {
            $position->activate();
        } else {
            $position->deactivate();
        }

        return $position;
    }
}
