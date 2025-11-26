<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Company\Application\Command\Position\UpdatePositionCommand;
use App\Module\Company\Application\Event\Position\PositionUpdatedEvent;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use App\Module\Company\Domain\Service\Position\PositionUpdater;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class UpdatePositionCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly PositionReaderInterface $positionReaderRepository,
        private readonly PositionUpdater $positionUpdater,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.position.update.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(UpdatePositionCommand $command): void
    {
        $this->validate($command);

        $position = $this->positionReaderRepository->getPositionByUUID($command->positionUUID);
        $this->positionUpdater->update(
            position: $position,
            name: $command->name,
            description: $command->description,
            active: $command->active,
            departmentsUUIDs: $command->departmentsUUIDs,
        );

        $this->eventDispatcher->dispatch(new PositionUpdatedEvent([
            UpdatePositionCommand::POSITION_UUID => $command->positionUUID,
            UpdatePositionCommand::POSITION_NAME => $command->name,
            UpdatePositionCommand::POSITION_DESCRIPTION => $command->description,
            UpdatePositionCommand::POSITION_ACTIVE => $command->active,
            UpdatePositionCommand::DEPARTMENTS_UUIDS => $command->departmentsUUIDs,
        ]));
    }
}
