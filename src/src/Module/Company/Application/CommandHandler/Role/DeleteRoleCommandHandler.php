<?php

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Module\Company\Application\Command\Role\DeleteRoleCommand;
use App\Module\Company\Domain\Service\Role\RoleDeleter;

readonly class DeleteRoleCommandHandler
{
    public function __construct(private RoleDeleter $roleDeleter)
    {
    }

    public function __invoke(DeleteRoleCommand $command): void
    {
        $this->roleDeleter->delete($command->getRole());
    }
}
