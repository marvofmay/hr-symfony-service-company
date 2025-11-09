<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Enum\Notification;

use App\Common\Domain\Interface\EnumInterface;

enum NotificationChannelEnum: string implements EnumInterface
{
    case INTERNAL = 'internal';
    case EMAIL = 'email';
    case SMS = 'sms';

    public function label(): string
    {
        return match ($this) {
            self::INTERNAL => 'notification.channel.internal',
            self::EMAIL => 'notification.channel.email',
            self::SMS => 'notification.channel.sms',
        };
    }

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }

    public const array VALUES = [
        self::INTERNAL->value,
        self::EMAIL->value,
        self::SMS->value,
    ];
}