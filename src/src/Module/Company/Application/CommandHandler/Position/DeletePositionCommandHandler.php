<?php

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Module\Company\Application\Command\Position\DeletePositionCommand;
use App\Module\Company\Application\Event\Position\PositionDeletedEvent;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Service\Position\PositionDeleter;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class DeletePositionCommandHandler
{
    public function __construct(private PositionDeleter $positionDeleter, private EventDispatcherInterface  $eventDispatcher,)
    {
    }

    public function __invoke(DeletePositionCommand $command): void
    {
        $this->positionDeleter->delete($command->getPosition());
        $this->eventDispatcher->dispatch(new PositionDeletedEvent([
            Position::COLUMN_UUID => $command->getPosition()->getUUID(),
        ]));
    }
}
