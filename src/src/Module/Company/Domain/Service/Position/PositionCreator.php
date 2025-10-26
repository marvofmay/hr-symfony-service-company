<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position;

use App\Module\Company\Application\Command\Position\CreatePositionCommand;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Interface\Position\PositionWriterInterface;
use App\Module\Company\Domain\Service\Position\Mapper\PositionDataMapper;

final readonly class PositionCreator
{
    public function __construct(
        private PositionWriterInterface $positionWriterRepository,
        private PositionDataMapper $positionDataMapper,
        private PositionDepartmentCreator $positionDepartmentCreator,
    ) {
    }

    public function create(CreatePositionCommand $command): void
    {
        $position = new Position();
        $this->positionDataMapper->map($position, $command);
        $this->positionDepartmentCreator->createDepartments($position, $command);
        $this->positionWriterRepository->savePositionInDB($position);
    }
}
