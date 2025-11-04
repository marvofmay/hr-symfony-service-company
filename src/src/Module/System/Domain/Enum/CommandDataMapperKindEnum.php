<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Enum;

use App\Common\Domain\Interface\EnumInterface;

enum CommandDataMapperKindEnum: string implements EnumInterface
{
    case COMMAND_MAPPER_INDUSTRY = 'command_mapper_industry';
    case COMMAND_MAPPER_POSITION = 'command_mapper_position';
    case COMMAND_MAPPER_ROLE = 'command_mapper_role';
    case COMMAND_MAPPER_CONTRACT_TYPE = 'command_mapper_contract_type';

    public function label(): string
    {
        return match ($this) {
            self::COMMAND_MAPPER_INDUSTRY => 'commandMapperIndustry',
            self::COMMAND_MAPPER_POSITION => 'commandMapperPosition',
            self::COMMAND_MAPPER_ROLE => 'commandMapperRole',
            self::COMMAND_MAPPER_CONTRACT_TYPE => 'commandMapperContractType',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
