<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Enum\RoleAccess;

enum RoleAccessEntityRelationFieldEnum: string
{
    case ROLE = 'role';
    case ACCESS = 'access';
}
