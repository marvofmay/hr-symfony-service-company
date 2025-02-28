<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Module\Company\Application\Command\Role\UpdateRoleCommand;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;

readonly class UpdateRoleCommandHandler
{
    public function __construct(private RoleWriterInterface $roleWriterRepository,)
    {
    }

    public function __invoke(UpdateRoleCommand $command): void
    {
        $role = $command->getRole();
        $role->setName($command->getName());
        $role->setDescription($command->getDescription());
        $role->setUpdatedAt(new \DateTime());

        $this->roleWriterRepository->updateRoleInDB($role);
    }
}
