<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Role;

use App\Module\Company\Application\Command\Role\UpdateRoleCommand;
use App\Module\Company\Domain\DTO\Role\UpdateDTO;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class UpdateRoleAction
{
    public function __construct(private readonly MessageBusInterface $commandBus, private readonly RoleReaderInterface $roleReaderRepository,)
    {
    }

    public function execute(UpdateDTO $updateDTO): void
    {

        $this->commandBus->dispatch(
            new UpdateRoleCommand(
                $updateDTO->getUUID(),
                $updateDTO->getName(),
                $updateDTO->getDescription(),
                $this->roleReaderRepository->getRoleByUUID($updateDTO->getUUID())
            )
        );
    }
}
