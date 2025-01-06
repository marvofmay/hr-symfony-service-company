<?php

declare(strict_types = 1);

namespace App\Application\CommandHandler\Role;

use App\Application\Command\Role\UpdateRoleCommand;
use App\Domain\Entity\Role;
use App\Domain\Service\Role\RoleService;

class UpdateRoleCommandHandler
{
    public function __construct(private readonly RoleService $roleWriterService, private Role $role) {}

    public function __invoke(UpdateroleCommand $command): void
    {
        $this->role = $command->getRole();
        $this->role->setName($command->getName());
        $this->role->setDescription($command->getDescription());

        $this->roleWriterService->updateroleInDB($this->role);
    }
}