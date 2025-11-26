<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Enum\Role;

enum RoleImportColumnEnum: int
{
    case ROLE_NAME = 0;
    case ROLE_DESCRIPTION = 1;

    case DYNAMIC_IS_ROLE_WITH_NAME_ALREADY_EXISTS = 100;

    public function label(): string
    {
        return match ($this) {
            self::ROLE_NAME => 'name',
            self::ROLE_DESCRIPTION => 'description',
            self::DYNAMIC_IS_ROLE_WITH_NAME_ALREADY_EXISTS => 'isRoleWithNameAlreadyExists',
        };
    }
}
