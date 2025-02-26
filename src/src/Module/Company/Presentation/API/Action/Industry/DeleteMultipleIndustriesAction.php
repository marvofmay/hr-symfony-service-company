<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Industry;

use App\Module\Company\Application\Command\Industry\DeleteMultipleIndustriesCommand;
use App\Module\Company\Domain\DTO\Industry\DeleteMultipleDTO;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class DeleteMultipleIndustriesAction
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function execute(DeleteMultipleDTO $deleteMultipleDTO): void
    {
        $this->commandBus->dispatch(
            new DeleteMultipleIndustriesCommand(
                $deleteMultipleDTO->getSelectedUUID(),
            )
        );
    }
}
