<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Enum;

use App\Common\Domain\Interface\EnumInterface;

enum CommandDataMapperKindEnum: string implements EnumInterface
{
    case COMMAND_MAPPER_INDUSTRY = 'command_mapper_industry';
    case COMMAND_MAPPER_POSITION = 'command_mapper_position';

    public function label(): string
    {
        return match ($this) {
            self::COMMAND_MAPPER_INDUSTRY => 'commandMapperIndustry',
            self::COMMAND_MAPPER_POSITION => 'commandMapperPosition',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
