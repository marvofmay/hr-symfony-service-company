<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position;

use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Interface\Position\PositionUpdaterInterface;
use App\Module\Company\Domain\Interface\Position\PositionWriterInterface;

final readonly class PositionUpdater implements PositionUpdaterInterface
{
    public function __construct(
        private PositionWriterInterface $positionWriterRepository,
        private PositionDepartmentUpdater $positionDepartmentUpdater,
    ) {
    }

    public function update(Position $position, string $name, ?string $description = null, bool $active = false, array $departmentsUUIDs = []): void
    {
        $position->rename($name);
        if (null !== $description) {
            $position->updateDescription($description);
        }
        $active ? $position->activate() : $position->deactivate();
        $this->positionDepartmentUpdater->updateDepartments($position, $departmentsUUIDs);

        $this->positionWriterRepository->savePositionInDB($position);
    }
}
