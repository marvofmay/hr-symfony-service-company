<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Module\Company\Application\Command\Position\DeleteMultiplePositionsCommand;
use App\Module\Company\Application\Event\Position\PositionMultipleDeletedEvent;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Service\Position\PositionMultipleDeleter;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class DeleteMultiplePositionsCommandHandler
{
    public function __construct(private PositionMultipleDeleter $multipleDeleter, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function __invoke(DeleteMultiplePositionsCommand $command): void
    {
        $this->multipleDeleter->multipleDelete($command->getPositions());
        $this->eventDispatcher->dispatch(new PositionMultipleDeletedEvent(
            $command->getPositions()->map(fn (Position $position) => $position->getUUID())->toArray(),
        ));
    }
}
