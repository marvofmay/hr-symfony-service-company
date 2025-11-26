<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\RoleAccessPermission;

use App\Common\Domain\Enum\DeleteTypeEnum;
use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Domain\Entity\Access;
use App\Module\System\Domain\Entity\Permission;

interface RoleAccessPermissionWriterInterface
{
    public function deleteRoleAccessPermissionsInDB(Role $role, Access $access, Permission $permission, DeleteTypeEnum $deleteTypeEnum = DeleteTypeEnum::SOFT_DELETE): void;
    public function deleteRoleAccessPermissionsByRoleAndAccessInDB(Role $role, Access $access, DeleteTypeEnum $deleteTypeEnum = DeleteTypeEnum::SOFT_DELETE): void;
}
