<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\RoleAccessPermission;

use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Domain\Entity\Access;
use App\Module\System\Domain\Entity\Permission;

interface RoleAccessPermissionInterface
{
    public function isRoleHasAccessAndPermission(Permission $permission, Access $access, Role $role): bool;
}