<?php

namespace App\Module\Company\Domain\Interface\Role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Domain\Entity\Access;

interface RoleAccessPermissionDeleterInterface
{
    public function delete(Role $role, Access $access): void;
}