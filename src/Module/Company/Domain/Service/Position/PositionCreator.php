<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position;

use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Interface\Position\PositionCreatorInterface;
use App\Module\Company\Domain\Interface\Position\PositionWriterInterface;

final readonly class PositionCreator implements PositionCreatorInterface
{
    public function __construct(
        private PositionWriterInterface $positionWriterRepository,
        private PositionDepartmentCreator $positionDepartmentCreator,
    ) {
    }

    public function create(string $name, ?string $description = null, bool $active = false, array $departmentsUUIDs = []): void
    {
        $position = Position::create(trim($name), $description, $active);

        if ([] !== $departmentsUUIDs) {
            $this->positionDepartmentCreator->createDepartments($position, $departmentsUUIDs);
        }

        $this->positionWriterRepository->savePositionInDB($position);
    }
}
