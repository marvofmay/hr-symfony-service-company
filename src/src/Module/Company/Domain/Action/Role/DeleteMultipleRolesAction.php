<?php

declare(strict_types = 1);

namespace App\Module\Company\Domain\Action\Role;

use App\Module\Company\Application\Command\Role\DeleteMultipleRolesCommand;
use App\Module\Company\Domain\DTO\Role\DeleteMultipleDTO;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class DeleteMultipleRolesAction
{
    public function __construct(private MessageBusInterface $commandBus) {}

    public function execute(DeleteMultipleDTO $deleteMultipleDTO): void
    {
        $this->commandBus->dispatch(
            new DeleteMultipleRolesCommand(
                $deleteMultipleDTO->getSelectedUUID(),
            )
        );
    }
}