<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Enum;

use App\Common\Domain\Interface\EnumInterface;

enum AccessEnum: string implements EnumInterface
{
    case COMPANY = ModuleEnum::COMPANY->value.'.company';
    case DEPARTMENT = ModuleEnum::COMPANY->value.'.department';
    case EMPLOYEE = ModuleEnum::COMPANY->value.'.employee';
    case INDUSTRY = ModuleEnum::COMPANY->value.'.industry';
    case ROLE = ModuleEnum::COMPANY->value.'.role';
    case POSITION = ModuleEnum::COMPANY->value.'.position';
    case CONTRACT_TYPE = ModuleEnum::COMPANY->value.'.contractType';
    case IMPORT = ModuleEnum::COMPANY->value.'.import';
    case SETTING = ModuleEnum::SYSTEM->value.'.setting';
    case ACCESS = ModuleEnum::SYSTEM->value.'.access';
    case PERMISSION = ModuleEnum::SYSTEM->value.'.permission';
    case NOTE = ModuleEnum::NOTE->value.'.note';

    public function label(): string
    {
        return match ($this) {
            self::COMPANY => 'Company',
            self::DEPARTMENT => 'Department',
            self::EMPLOYEE => 'Employee',
            self::INDUSTRY => 'Industry',
            self::ROLE => 'Role',
            self::POSITION => 'Position',
            self::CONTRACT_TYPE => 'Contract Type',
            self::IMPORT => 'Import',
            self::SETTING => 'Setting',
            self::ACCESS => 'Access',
            self::PERMISSION => 'Permission',
            self::NOTE => 'Note',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
