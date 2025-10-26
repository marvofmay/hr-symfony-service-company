<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position;

use App\Module\Company\Application\Command\Position\CreatePositionCommand;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Interface\Position\PositionWriterInterface;
use App\Module\System\Domain\Enum\CommandDataMapperKindEnum;
use App\Module\System\Domain\Factory\CommandDataMapperFactory;

final readonly class PositionCreator
{
    public function __construct(
        private PositionWriterInterface $positionWriterRepository,
        private PositionDepartmentCreator $positionDepartmentCreator,
        private CommandDataMapperFactory $commandDataMapperFactory,
    ) {
    }

    public function create(CreatePositionCommand $command): void
    {
        $position = new Position();
        $mapper = $this->commandDataMapperFactory->getMapper(CommandDataMapperKindEnum::COMMAND_MAPPER_POSITION);
        $mapper->map($position, $command);
        $this->positionDepartmentCreator->createDepartments($position, $command);
        $this->positionWriterRepository->savePositionInDB($position);
    }
}
