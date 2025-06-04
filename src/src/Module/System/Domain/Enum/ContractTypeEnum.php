<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Enum;

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

    public function description(): string
    {
        return match ($this) {
            self::EMPLOYMENT  => 'Standard employment contract',
            self::B2B         => 'Business-to-business contract',
            self::INTERNSHIP  => 'Internship or student program',
            self::CONTRACTOR  => 'Independent contractor agreement',
            self::TEMPORARY   => 'Temporary work contract',
            self::PART_TIME   => 'Part-time employment',
            self::FULL_TIME   => 'Full-time employment',
            self::COMMISSION  => 'Commission-based contract',
            self::CONSULTING  => 'Consulting service agreement',
            self::FREELANCE   => 'Freelance work contract',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}