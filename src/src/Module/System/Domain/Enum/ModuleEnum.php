<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Enum;

use App\Common\Domain\Interface\EnumInterface;

enum ModuleEnum: string implements EnumInterface
{
    case SYSTEM = 'system';
    case COMPANY = 'company';
    case NOTE = 'note';
    case TASK = 'task';

    public function label(): string
    {
        return match ($this) {
            self::SYSTEM => self::SYSTEM->value,
            self::COMPANY => self::COMPANY->value,
            self::NOTE => self::NOTE->value,
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
