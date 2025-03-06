<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Module\Company\Application\Command\Role\DeleteMultipleRolesCommand;
use App\Module\Company\Domain\Service\Role\RoleMultipleDeleter;

readonly class DeleteMultipleRolesCommandHandler
{
    public function __construct(private RoleMultipleDeleter $roleMultipleDeleter,)
    {
    }

    public function __invoke(DeleteMultipleRolesCommand $command): void
    {
       $this->roleMultipleDeleter->multipleDelete($command->roles);
    }
}
