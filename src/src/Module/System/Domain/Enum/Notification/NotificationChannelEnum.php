<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Enum\Notification;

enum NotificationChannelEnum: string
{
    case INTERNAL = 'internal';
    case EMAIL = 'email';
    case SMS = 'sms';
}