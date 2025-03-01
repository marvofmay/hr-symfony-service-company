<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Module\Company\Application\Command\Role\CreateRoleCommand;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;

readonly class CreateRoleCommandHandler
{
    public function __construct(private Role $role, private RoleWriterInterface $roleWriterRepository,)
    {
    }

    public function __invoke(CreateRoleCommand $command): void
    {
        $this->role->setName($command->getName());
        $this->role->setDescription($command->getDescription());

        $this->roleWriterRepository->saveRoleInDB($this->role);
    }
}
