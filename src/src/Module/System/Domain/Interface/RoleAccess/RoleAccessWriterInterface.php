<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\RoleAccess;

use App\Common\Domain\Enum\DeleteTypeEnum;
use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Domain\Entity\Access;

interface RoleAccessWriterInterface
{
    public function deleteRoleAccessInDB(Role $role, Access $access, DeleteTypeEnum $deleteTypeEnum = DeleteTypeEnum::SOFT_DELETE): void;
    public function deleteRoleAccessesByRoleInDB(Role $role, DeleteTypeEnum $deleteTypeEnum = DeleteTypeEnum::SOFT_DELETE): void;
}
