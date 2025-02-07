<?php

declare(strict_types = 1);

namespace App\Module\Note\Domain\Enum;

enum NotePriorityEnum: string
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
}
