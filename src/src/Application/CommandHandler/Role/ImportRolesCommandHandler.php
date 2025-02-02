<?php

declare(strict_types = 1);

namespace App\Application\CommandHandler\Role;

use App\Application\Command\Role\ImportRolesCommand;
use App\Domain\Entity\Role;
use App\Domain\Service\Role\RoleService;

readonly class ImportRolesCommandHandler
{
    public function __construct(private RoleService $roleService) { }

    public function __invoke(ImportRolesCommand $command): void
    {
        $roles = [];
        foreach ($command->data as $item) {
            $role = new Role();
            $role->setName($item[0]);
            $role->setDescription($item[0]);

            $roles[] = $role;
        }

        $this->roleService->saveRolesInDB($roles);
    }
}