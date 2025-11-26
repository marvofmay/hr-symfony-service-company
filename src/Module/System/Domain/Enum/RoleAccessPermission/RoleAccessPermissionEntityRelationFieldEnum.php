<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Enum\RoleAccessPermission;

enum RoleAccessPermissionEntityRelationFieldEnum: string
{
    case ROLE = 'role';
    case ACCESS = 'access';
    case PERMISSION = 'permission';
}
