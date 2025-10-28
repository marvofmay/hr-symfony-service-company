<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Company\Application\Command\Position\CreatePositionCommand;
use App\Module\Company\Application\Event\Position\PositionCreatedEvent;
use App\Module\Company\Domain\Service\Position\PositionCreator;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class CreatePositionCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly PositionCreator $positionCreator,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.position.create.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(CreatePositionCommand $command): void
    {
        $this->validate($command);

        $this->positionCreator->create($command);
        $this->eventDispatcher->dispatch(new PositionCreatedEvent([
            CreatePositionCommand::POSITION_NAME => $command->name,
            CreatePositionCommand::POSITION_DESCRIPTION => $command->description,
            CreatePositionCommand::POSITION_ACTIVE => $command->active,
            CreatePositionCommand::DEPARTMENTS_UUIDS => $command->departmentsUUIDs,
        ]));
    }
}
