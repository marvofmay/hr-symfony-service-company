<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Position;

use App\Module\Company\Application\Command\Position\DeletePositionCommand;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class DeletePositionAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private PositionReaderInterface $positionReaderRepository,
    ) {
    }

    public function execute(string $uuid): void
    {
        try {
            $this->commandBus->dispatch(new DeletePositionCommand($this->positionReaderRepository->getPositionByUUID($uuid)));
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
