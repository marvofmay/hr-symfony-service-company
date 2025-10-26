<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position;

use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Interface\Position\PositionWriterInterface;
use App\Module\Company\Domain\Interface\PositionDepartment\PositionDepartmentReaderInterface;

final readonly class PositionRestorer
{
    public function __construct(
        private PositionDepartmentReaderInterface $positionDepartmentReaderRepository,
        private PositionWriterInterface $positionWriterRepository,
    ) {
    }

    public function restore(Position $position): void
    {
        $position->deletedAt = null;

        $positionUUID = $position->getUUID()->toString();
        $positonDepartments = $this->positionDepartmentReaderRepository->getDeletedPositionDepartmentsByPositionUUID($positionUUID);
        foreach ($positonDepartments as $positionDepartment) {
            $positionDepartment->deletedAt = null;
        }

        $this->positionWriterRepository->savePositionInDB($position);
    }
}
