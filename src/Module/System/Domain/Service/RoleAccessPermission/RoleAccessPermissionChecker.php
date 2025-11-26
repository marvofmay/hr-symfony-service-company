<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Service\RoleAccessPermission;

use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Domain\Entity\Access;
use App\Module\System\Domain\Entity\Permission;
use App\Module\System\Domain\Interface\RoleAccessPermission\RoleAccessPermissionReaderInterface;

readonly class RoleAccessPermissionChecker
{
    public function __construct(private RoleAccessPermissionReaderInterface $roleAccessPermissionReaderRepository)
    {
    }

    public function check(Permission $permission, Access $access, Role $role): bool
    {
        return $this->roleAccessPermissionReaderRepository->isRoleHasAccessAndPermission($permission, $access, $role);
    }
}
