<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Role;

use App\Module\Company\Application\Command\Role\UpdateRoleCommand;

interface RoleUpdaterInterface
{
    public function update(UpdateRoleCommand $command): void;
}