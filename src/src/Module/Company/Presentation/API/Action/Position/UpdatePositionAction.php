<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Position;

use App\Module\Company\Application\Command\Position\UpdatePositionCommand;
use App\Module\Company\Domain\DTO\Position\UpdateDTO;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class UpdatePositionAction
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private PositionReaderInterface $positionReaderRepository,
    ){}

    public function execute(UpdateDTO $updateDTO): void
    {
        $this->commandBus->dispatch(
            new UpdatePositionCommand(
                $updateDTO->getUUID(),
                $updateDTO->getName(),
                $updateDTO->getDescription(),
                $updateDTO->getActive(),
                $updateDTO->getDepartmentsUUID(),
                $this->positionReaderRepository->getPositionByUUID($updateDTO->getUUID())
            )
        );
    }
}
