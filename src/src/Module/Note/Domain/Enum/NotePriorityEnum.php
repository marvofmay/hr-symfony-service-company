<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\Enum;

use App\Common\Domain\Interface\EnumInterface;

enum NotePriorityEnum: string implements EnumInterface
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'notice.lowPriority',
            self::MEDIUM => 'notice.mediumPriority',
            self::HIGH => 'notice.highPriority',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
