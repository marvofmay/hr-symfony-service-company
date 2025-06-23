<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Module\Company\Application\Command\Role\CreateRoleAccessPermissionCommand;
use App\Module\Company\Domain\Service\Role\RoleAccessPermissionCreator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
readonly class CreateRoleAccessPermissionCommandHandler
{
    public function __construct(private RoleAccessPermissionCreator $roleAccessPermissionCreator)
    {
    }

    public function __invoke(CreateRoleAccessPermissionCommand $command): void
    {
        $this->roleAccessPermissionCreator->create($command->getRole(), $command->getAccesses());
    }
}
