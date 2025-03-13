<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Enum;

use App\Common\Domain\Interface\EnumInterface;

enum ContactTypeEnum: string implements EnumInterface
{
    case PHONE = 'phone';
    case EMAIL = 'email';
    case WEBSITE = 'website';

    public function label(): string
    {
        return match ($this) {
            self::PHONE   => 'lowPriority',
            self::EMAIL   => 'mediumPriority',
            self::WEBSITE => 'highPriority',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
