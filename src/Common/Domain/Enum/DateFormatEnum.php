<?php

declare(strict_types=1);

namespace App\Common\Domain\Enum;

use App\Common\Domain\Interface\EnumInterface;

enum DateFormatEnum: string implements EnumInterface
{
    case DD_MM_YYYY = 'dd-mm-yyyy';
    case YYYY_MM_DD = 'yyyy-mm-dd';

    public function label(): string
    {
        return $this->value;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
