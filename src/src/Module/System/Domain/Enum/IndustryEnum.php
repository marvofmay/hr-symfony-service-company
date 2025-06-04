<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Enum;

use App\Common\Domain\Interface\EnumInterface;

enum IndustryEnum: string implements EnumInterface
{
    case TECHNOLOGY       = 'technology';
    case HEALTHCARE       = 'healthcare';
    case FINANCE          = 'finance';
    case EDUCATION        = 'education';
    case MANUFACTURING    = 'manufacturing';
    case RETAIL           = 'retail';
    case ENERGY           = 'energy';
    case TRANSPORTATION   = 'transportation';
    case CONSTRUCTION     = 'construction';
    case HOSPITALITY      = 'hospitality';

    public function label(): string
    {
        return $this->value;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function description(): string
    {
        return match ($this) {
            self::TECHNOLOGY     => 'Technology and software development sector',
            self::HEALTHCARE     => 'Healthcare services and medical industry',
            self::FINANCE        => 'Banking, investment, and financial services',
            self::EDUCATION      => 'Educational institutions and training services',
            self::MANUFACTURING  => 'Production and industrial manufacturing',
            self::RETAIL         => 'Retail and consumer sales businesses',
            self::ENERGY         => 'Energy production, including renewable sources',
            self::TRANSPORTATION => 'Logistics, shipping, and transportation services',
            self::CONSTRUCTION   => 'Construction and infrastructure development',
            self::HOSPITALITY    => 'Hotels, restaurants, and leisure industry',
        };
    }
}
