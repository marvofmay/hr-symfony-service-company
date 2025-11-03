<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Domain\Entity\Access;

interface RoleAccessPermissionUpdaterInterface
{
    public function updateAccessesPermission(Role $role, Access $access, array $permissionsUUIDs): void;
}