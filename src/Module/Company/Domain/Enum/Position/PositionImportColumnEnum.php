<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Enum\Position;

enum PositionImportColumnEnum: int
{
    case POSITION_NAME = 0;
    case POSITION_DESCRIPTION = 1;
    case POSITION_ACTIVE = 2;
    case DEPARTMENT_INTERNAL_CODE = 3;

    case DYNAMIC_IS_POSITION_WITH_NAME_ALREADY_EXISTS = 100;

    public function label(): string
    {
        return match ($this) {
            self::POSITION_NAME => 'name',
            self::POSITION_DESCRIPTION => 'description',
            self::POSITION_ACTIVE => 'active',
            self::DEPARTMENT_INTERNAL_CODE => 'departmentInternalCode',
            self::DYNAMIC_IS_POSITION_WITH_NAME_ALREADY_EXISTS => 'isPositionWithNameAlreadyExists',
        };
    }
}
