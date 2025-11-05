<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position;

use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Interface\Position\PositionRestorerInterface;
use App\Module\Company\Domain\Interface\Position\PositionWriterInterface;
use App\Module\Company\Domain\Interface\PositionDepartment\PositionDepartmentReaderInterface;

final readonly class PositionRestorer implements PositionRestorerInterface
{
    public function __construct(
        private PositionDepartmentReaderInterface $positionDepartmentReaderRepository,
        private PositionWriterInterface $positionWriterRepository,
    ) {
    }

    public function restore(Position $position): void
    {
        $position->setDeletedAt(null);

        $positionUUID = $position->getUUID()->toString();
        $positonDepartments = $this->positionDepartmentReaderRepository->getDeletedPositionDepartmentsByPositionUUID($positionUUID);
        foreach ($positonDepartments as $positionDepartment) {
            $positionDepartment->setDeletedAt(null);
        }

        $this->positionWriterRepository->savePositionInDB($position);
    }
}
