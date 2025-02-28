<?php

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Module\Company\Application\Command\Role\DeleteRoleCommand;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;

readonly class DeleteRoleCommandHandler
{
    public function __construct(private RoleWriterInterface $roleWriterRepository,)
    {
    }

    public function __invoke(DeleteRoleCommand $command): void
    {
        $this->roleWriterRepository->deleteRoleInDB($command->getRole());
    }
}
