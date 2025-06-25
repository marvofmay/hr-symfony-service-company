<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Module\Company\Application\Command\Position\CreatePositionCommand;
use App\Module\Company\Application\Event\Position\PositionCreatedEvent;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Service\Position\PositionCreator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class CreatePositionCommandHandler
{
    public function __construct(private PositionCreator $positionCreator, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function __invoke(CreatePositionCommand $command): void
    {
        $this->positionCreator->create($command);
        $this->eventDispatcher->dispatch(new PositionCreatedEvent([
            Position::COLUMN_NAME        => $command->name,
            Position::COLUMN_DESCRIPTION => $command->description,
            Position::COLUMN_ACTIVE      => $command->active,
        ]));
    }
}
