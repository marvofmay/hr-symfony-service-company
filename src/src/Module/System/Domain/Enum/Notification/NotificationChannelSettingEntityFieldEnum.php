<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Enum\Notification;

enum NotificationChannelSettingEntityFieldEnum: string
{
    case CHANNEL = 'channel';
    case ENABLED = 'enabled';
}
