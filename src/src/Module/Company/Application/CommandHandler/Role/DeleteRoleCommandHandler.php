<?php

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Module\Company\Application\Command\Role\DeleteRoleCommand;
use App\Module\Company\Domain\Service\Role\RoleService;

readonly class DeleteRoleCommandHandler
{
    public function __construct(private RoleService $roleService)
    {
    }

    public function __invoke(DeleteRoleCommand $command): void
    {
        $this->roleService->deleteRoleInDB($command->getRole());
    }
}
