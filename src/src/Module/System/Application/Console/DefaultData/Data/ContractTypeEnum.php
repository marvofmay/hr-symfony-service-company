<?php

declare(strict_types=1);

namespace App\Module\System\Application\Console\DefaultData\Data;

use App\Common\Domain\Interface\EnumInterface;

enum ContractTypeEnum: string implements EnumInterface
{
    case EMPLOYMENT  = 'employment';
    case B2B         = 'b2b';
    case INTERNSHIP  = 'internship';
    case CONTRACTOR  = 'contractor';
    case TEMPORARY   = 'temporary';
    case PART_TIME   = 'part_time';
    case FULL_TIME   = 'full_time';
    case COMMISSION  = 'commission';
    case CONSULTING  = 'consulting';
    case FREELANCE   = 'freelance';

    public function label(): string
    {
        return $this->value;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}