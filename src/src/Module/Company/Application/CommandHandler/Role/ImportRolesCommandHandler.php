<?php

declare(strict_types = 1);

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Module\Company\Application\Command\Role\ImportRolesCommand;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Service\Role\RoleService;

readonly class ImportRolesCommandHandler
{
    public function __construct(private RoleService $roleService) {}

    public function __invoke(ImportRolesCommand $command): void
    {
        $roles = [];
        foreach ($command->data as $item) {
            $role = new Role();
            $role->setName($item[0]);
            $role->setDescription($item[1]);

            $roles[] = $role;
        }

        $this->roleService->saveRolesInDB($roles);
    }
}