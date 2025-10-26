<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position;

use App\Module\Company\Application\Command\Position\UpdatePositionCommand;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use App\Module\Company\Domain\Interface\Position\PositionWriterInterface;
use App\Module\System\Domain\Enum\CommandDataMapperKindEnum;
use App\Module\System\Domain\Factory\CommandDataMapperFactory;

final readonly class PositionUpdater
{
    public function __construct(
        private PositionReaderInterface $positionReaderRepository,
        private PositionWriterInterface $positionWriterRepository,
        private CommandDataMapperFactory $commandDataMapperFactory,
        private PositionDepartmentUpdater $positionDepartmentUpdater,
    ) {
    }

    public function update(UpdatePositionCommand $command): void
    {
        $position = $this->positionReaderRepository->getPositionByUUID($command->positionUUID);
        $mapper = $this->commandDataMapperFactory->getMapper(CommandDataMapperKindEnum::COMMAND_MAPPER_POSITION);
        $mapper->map($position, $command);
        $this->positionDepartmentUpdater->updateDepartments($position, $command);
        $this->positionWriterRepository->savePositionInDB($position);
    }
}
