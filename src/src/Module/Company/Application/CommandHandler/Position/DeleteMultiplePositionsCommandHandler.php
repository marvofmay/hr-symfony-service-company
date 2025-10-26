<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Company\Application\Command\Position\DeleteMultiplePositionsCommand;
use App\Module\Company\Application\Event\Position\PositionMultipleDeletedEvent;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use App\Module\Company\Domain\Service\Position\PositionMultipleDeleter;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class DeleteMultiplePositionsCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly PositionReaderInterface $positionReaderRepository,
        private readonly PositionMultipleDeleter $multipleDeleter,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.position.delete_multiple.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(DeleteMultiplePositionsCommand $command): void
    {
        $this->validate($command);

        $positions = $this->positionReaderRepository->getPositionsByUUID($command->positionsUUIDs);

        $this->multipleDeleter->multipleDelete($positions);
        $this->eventDispatcher->dispatch(new PositionMultipleDeletedEvent([
            DeleteMultiplePositionsCommand::POSITIONS_UUIDS => $command->positionsUUIDs,
        ]));
    }
}
