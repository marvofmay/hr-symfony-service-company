<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Domain\Entity\Access;

interface RoleAccessPermissionAssignerInterface
{
    public function assign(Role $role, Access $access, array $permissionsUUIDs): void;
}