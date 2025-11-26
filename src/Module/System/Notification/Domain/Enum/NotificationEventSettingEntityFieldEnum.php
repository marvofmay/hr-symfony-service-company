<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Enum;

enum NotificationEventSettingEntityFieldEnum: string
{
    case EVENT_NAME = 'eventName';
    case ENABLED = 'enabled';
}
