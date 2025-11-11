<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Enum;

enum NotificationTemplateSettingEntityRelationFieldEnum: string
{
    case EVENT = 'event';
    case CHANNEL = 'channel';
}
