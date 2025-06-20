<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Module\Company\Application\Command\Role\CreateRoleAccessCommand;
use App\Module\Company\Domain\Service\Role\RoleAccessCreator;

readonly class CreateRoleAccessCommandHandler
{
    public function __construct(private RoleAccessCreator $roleAccessCreator)
    {
    }

    public function __invoke(CreateRoleAccessCommand $command): void
    {
        $this->roleAccessCreator->create($command->getRole(), $command->getAccesses());
    }
}
