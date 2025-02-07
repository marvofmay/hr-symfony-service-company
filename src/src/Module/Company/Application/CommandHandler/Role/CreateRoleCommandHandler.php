<?php

declare(strict_types = 1);

namespace App\module\company\Application\CommandHandler\Role;

use App\module\company\Application\Command\Role\CreateRoleCommand;
use App\module\company\Domain\Entity\Role;
use App\module\company\Domain\Service\Role\RoleService;

readonly class CreateRoleCommandHandler
{
    public function __construct(private RoleService $roleService) { }

    public function __invoke(CreateRoleCommand $command): void
    {
        $role = new Role();
        $role->setName($command->name);
        $role->setDescription($command->description);

        $this->roleService->saveRoleInDB($role);
    }
}