<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\RoleAccess;

use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Domain\Entity\Access;

interface RoleAccessInterface
{
    public function isRoleHasAccess(Access $access, Role $role): bool;
}