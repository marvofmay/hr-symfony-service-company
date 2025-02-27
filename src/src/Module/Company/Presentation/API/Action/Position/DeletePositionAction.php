<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Position;

use App\Module\Company\Application\Command\Position\DeletePositionCommand;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class DeletePositionAction
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private Position $position,
        private PositionReaderInterface $positionReaderRepository)
    {
    }

    public function setPositionToDelete(Position $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function execute(string $uuid): void
    {
        $this->commandBus->dispatch(new DeletePositionCommand($this->positionReaderRepository->getPositionByUUID($uuid)));
    }
}
