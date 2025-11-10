<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Enum;

enum NotificationChannelSettingEntityFieldEnum: string
{
    case CHANNEL_CODE = 'channelCode';
    case ENABLED = 'enabled';
}
