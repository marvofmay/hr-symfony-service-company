<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Module\Company\Application\Command\Role\CreateRoleCommand;
use App\Module\Company\Domain\Service\Role\RoleCreator;

readonly class CreateRoleCommandHandler
{
    public function __construct(private RoleCreator $roleCreator,)
    {
    }

    public function __invoke(CreateRoleCommand $command): void
    {
        $this->roleCreator->create($command->getName(), $command->getDescription());
    }
}
