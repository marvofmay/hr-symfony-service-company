<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Role;

use App\Module\Company\Application\Command\Role\CreateRoleCommand;

interface RoleCreatorInterface
{
    public function create(CreateRoleCommand $command): void;
}