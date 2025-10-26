<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position;

use App\Module\Company\Application\Command\Position\UpdatePositionCommand;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use App\Module\Company\Domain\Interface\Position\PositionWriterInterface;
use App\Module\Company\Domain\Service\Position\Mapper\PositionDataMapper;

final readonly class PositionUpdater
{
    public function __construct(
        private PositionReaderInterface $positionReaderRepository,
        private PositionWriterInterface $positionWriterRepository,
        private PositionDataMapper $positionDataMapper,
        private PositionDepartmentUpdater $positionDepartmentUpdater,
    ) {
    }

    public function update(UpdatePositionCommand $command): void
    {
        $position = $this->positionReaderRepository->getPositionByUUID($command->positionUUID);
        $this->positionDataMapper->map($position, $command);
        $this->positionDepartmentUpdater->updateDepartments($position, $command);
        $this->positionWriterRepository->savePositionInDB($position);
    }
}
