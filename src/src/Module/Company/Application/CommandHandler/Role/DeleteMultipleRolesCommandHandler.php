<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Module\Company\Application\Command\Role\DeleteMultipleRolesCommand;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;

readonly class DeleteMultipleRolesCommandHandler
{
    public function __construct(private RoleWriterInterface $roleWriterRepository,)
    {
    }

    public function __invoke(DeleteMultipleRolesCommand $command): void
    {
        $this->roleWriterRepository->deleteMultipleRolesInDB($command->roles);
    }
}
