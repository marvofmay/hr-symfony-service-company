<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Role;

use App\Module\Company\Application\Command\Role\DeleteMultipleRolesCommand;
use App\Module\Company\Domain\DTO\Role\DeleteMultipleDTO;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class DeleteMultipleRolesAction
{
    public function __construct(private MessageBusInterface $commandBus, private RoleReaderInterface $roleReaderRepository)
    {
    }

    public function execute(DeleteMultipleDTO $deleteMultipleDTO): void
    {
        $this->commandBus->dispatch(
            new DeleteMultipleRolesCommand(
                $this->roleReaderRepository->getRolesByUUID($deleteMultipleDTO->getSelectedUUID())
            )
        );
    }
}
