<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Module\Company\Application\Command\Role\CreateRoleCommand;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Service\Role\RoleService;

readonly class CreateRoleCommandHandler
{
    public function __construct(private RoleService $roleService)
    {
    }

    public function __invoke(CreateRoleCommand $command): void
    {
        $role = new Role();
        $role->setName($command->name);
        $role->setDescription($command->description);

        $this->roleService->saveRoleInDB($role);
    }
}
