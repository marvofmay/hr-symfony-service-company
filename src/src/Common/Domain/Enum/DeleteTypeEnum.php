<?php

declare(strict_types=1);

namespace App\Common\Domain\Enum;

use App\Common\Domain\Interface\EnumInterface;

enum DeleteTypeEnum: string implements EnumInterface
{
    case HARD_DELETE = 'hard';
    case SOFT_DELETE = 'soft';

    public function label(): string
    {
        return $this->value;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
