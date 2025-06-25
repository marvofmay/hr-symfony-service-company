<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Module\Company\Application\Command\Position\UpdatePositionCommand;
use App\Module\Company\Application\Event\Position\PositionUpdatedEvent;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Service\Position\PositionUpdater;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class UpdatePositionCommandHandler
{
    public function __construct(private PositionUpdater $positionUpdater, private EventDispatcherInterface $eventDispatcher,)
    {
    }

    public function __invoke(UpdatePositionCommand $command): void
    {
        $this->positionUpdater->update($command);
        $this->eventDispatcher->dispatch(new PositionUpdatedEvent([
            Position::COLUMN_UUID        => $command->getPosition()->getUUID(),
            Position::COLUMN_NAME        => $command->getName(),
            Position::COLUMN_DESCRIPTION => $command->getDescription(),
        ]));
    }
}
