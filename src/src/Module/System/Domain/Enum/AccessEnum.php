<?php

namespace App\Module\System\Domain\Enum;

use App\Common\Domain\Interface\EnumInterface;

enum AccessEnum: string implements EnumInterface
{
    case COMPANY       = ModuleEnum::COMPANY->value . '.company';
    case DEPARTMENT    = ModuleEnum::COMPANY->value . '.department';
    case EMPLOYEE      = ModuleEnum::COMPANY->value . '.employee';
    case INDUSTRY      = ModuleEnum::COMPANY->value . '.industry';
    case ROLE          = ModuleEnum::COMPANY->value . '.role';
    case POSITION      = ModuleEnum::COMPANY->value . '.position';
    case CONTRACT_TYPE = ModuleEnum::COMPANY->value . '.contractType';
    case IMPORT        = ModuleEnum::COMPANY->value . '.import';
    case SETTING       = ModuleEnum::SYSTEM->value . '.setting';
    case NOTE          = ModuleEnum::NOTE->value . '.note';

    public function label(): string
    {
        return match ($this) {
            self::COMPANY => self::COMPANY->value,
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}