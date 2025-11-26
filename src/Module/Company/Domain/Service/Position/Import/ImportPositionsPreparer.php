<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position\Import;

use App\Module\Company\Domain\Enum\Position\PositionImportColumnEnum;

final readonly class ImportPositionsPreparer
{
    public function prepare(iterable $rows, array $existingPositions): array
    {
        $positionNameMap = [];
        $preparedRows = [];

        foreach ($rows as $row) {
            $name = trim((string) $row[PositionImportColumnEnum::POSITION_NAME->value]);
            $existingPosition = $existingPositions[$name] ?? null;
            $row[PositionImportColumnEnum::DYNAMIC_IS_POSITION_WITH_NAME_ALREADY_EXISTS->value] = null !== $existingPosition;

            if (!isset($positionNameMap[$name])) {
                $positionNameMap[$name] = $existingPosition ? $existingPosition->getUUID()->toString() : null;
            }

            $preparedRows[] = $row;
        }

        return [$preparedRows, $positionNameMap];
    }
}
