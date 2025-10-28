<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position\Import;

use App\Common\Infrastructure\Cache\EntityReferenceCache;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Enum\Position\PositionImportColumnEnum;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;

final readonly class ImportPositionsPreparer
{
    public function __construct(
        private PositionReaderInterface $positionReaderRepository,
        private EntityReferenceCache $entityReferenceCache,
    ) {
    }

    public function prepare(iterable $rows): array
    {
        $positionNameMap = [];
        $preparedRows = [];

        foreach ($rows as $row) {
            $name = trim((string) $row[PositionImportColumnEnum::POSITION_NAME->value]);
            $existingPosition = $this->entityReferenceCache->get(
                Position::class,
                $name,
                fn (string $name) => $this->positionReaderRepository->getPositionByName($name)
            );

            $row[PositionImportColumnEnum::DYNAMIC_IS_POSITION_WITH_NAME_ALREADY_EXISTS->value] = null !== $existingPosition;

            if (!isset($positionNameMap[$name])) {
                $positionNameMap[$name] = $existingPosition ? $existingPosition->getUUID()->toString() : null;
            }

            $row[PositionImportColumnEnum::DYNAMIC_POSITION_UUID->value] = $positionNameMap[$name];
            $preparedRows[] = $row;
        }

        return [$preparedRows, $positionNameMap];
    }
}
