<?php

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Company\Application\Command\Position\DeletePositionCommand;
use App\Module\Company\Application\Event\Position\PositionDeletedEvent;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use App\Module\Company\Domain\Service\Position\PositionDeleter;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class DeletePositionCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly PositionReaderInterface $positionReaderRepository,
        private readonly PositionDeleter $positionDeleter,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.position.delete.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(DeletePositionCommand $command): void
    {
        $this->validate($command);

        $position = $this->positionReaderRepository->getPositionByUUID($command->positionUUID);

        $this->positionDeleter->delete($position);
        $this->eventDispatcher->dispatch(new PositionDeletedEvent([
            DeletePositionCommand::POSITION_UUID => $command->positionUUID,
        ]));
    }
}
