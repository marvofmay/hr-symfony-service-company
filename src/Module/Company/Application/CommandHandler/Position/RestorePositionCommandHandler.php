<?php

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Company\Application\Command\Position\RestorePositionCommand;
use App\Module\Company\Application\Event\Position\PositionRestoredEvent;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use App\Module\Company\Domain\Service\Position\PositionRestorer;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class RestorePositionCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly PositionReaderInterface $positionReaderRepository,
        private readonly PositionRestorer $positionRestorer,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.position.restore.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(RestorePositionCommand $command): void
    {
        $this->validate($command);

        $position = $this->positionReaderRepository->getDeletedPositionByUUID($command->positionUUID);
        $this->positionRestorer->restore($position);
        $this->eventDispatcher->dispatch(new PositionRestoredEvent([
            RestorePositionCommand::POSITION_UUID => $command->positionUUID,
        ]));
    }
}
