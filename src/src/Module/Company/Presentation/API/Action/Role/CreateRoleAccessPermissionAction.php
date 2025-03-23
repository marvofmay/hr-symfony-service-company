<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Role;

use App\Module\Company\Application\Command\Role\CreateRoleAccessPermissionCommand;
use App\Module\Company\Domain\DTO\Role\CreateAccessPermissionDTO;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CreateRoleAccessPermissionAction
{
    public function __construct(private MessageBusInterface $commandBus,)
    {
    }

    public function execute(CreateAccessPermissionDTO $createAccessPermissionDTO): void
    {
        $this->commandBus->dispatch(
            new CreateRoleAccessPermissionCommand(
                $createAccessPermissionDTO->getRoleUUID(),
                $createAccessPermissionDTO->getAccesses(),
            )
        );
    }
}
