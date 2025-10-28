<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Enum\Role;

enum RoleEntityFieldEnum: string
{
    case UUID = 'uuid';
    case NAME = 'name';
    case DESCRIPTION = 'description';
}
