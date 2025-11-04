<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Enum\ContractType;

enum ContractTypeImportColumnEnum: int
{
    case CONTRACT_TYPE_NAME = 0;
    case CONTRACT_TYPE_DESCRIPTION = 1;
    case CONTRACT_TYPE_ACTIVE = 2;

    case DYNAMIC_IS_CONTRACT_TYPE_WITH_NAME_ALREADY_EXISTS = 100;

    public function label(): string
    {
        return match ($this) {
            self::CONTRACT_TYPE_NAME => 'name',
            self::CONTRACT_TYPE_DESCRIPTION => 'description',
            self::CONTRACT_TYPE_ACTIVE => 'active',
            self::DYNAMIC_IS_CONTRACT_TYPE_WITH_NAME_ALREADY_EXISTS => 'isRoleWithNameAlreadyExists',
        };
    }
}
