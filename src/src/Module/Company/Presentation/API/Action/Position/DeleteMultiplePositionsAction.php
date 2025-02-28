<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Position;

use App\Module\Company\Application\Command\Position\DeleteMultiplePositionsCommand;
use App\Module\Company\Domain\DTO\Position\DeleteMultipleDTO;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class DeleteMultiplePositionsAction
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function execute(DeleteMultipleDTO $deleteMultipleDTO): void
    {
        $this->commandBus->dispatch(
            new DeleteMultiplePositionsCommand(
                $deleteMultipleDTO->getSelectedUUID(),
            )
        );
    }
}
