<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Enum\Permission;

enum PermissionEntityFieldEnum: string
{
    case UUID = 'uuid';
    case NAME = 'name';
    case DESCRIPTION = 'description';
    case ACTIVE = 'active';
}
