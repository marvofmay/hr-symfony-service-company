<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Module\Company\Application\Command\Role\UpdateRoleCommand;
use App\Module\Company\Domain\Service\Role\RoleService;

readonly class UpdateRoleCommandHandler
{
    public function __construct(private RoleService $roleWriterService)
    {
    }

    public function __invoke(UpdateRoleCommand $command): void
    {
        $role = $command->getRole();
        $role->setName($command->getName());
        $role->setDescription($command->getDescription());
        $role->setUpdatedAt(new \DateTime());

        $this->roleWriterService->updateRoleInDB($role);
    }
}
