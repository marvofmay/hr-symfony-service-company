<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Enum;

enum NotificationMessageEntityRelationFieldEnum: string
{
    case EVENT = 'event';
    case CHANNEL = 'channel';
    case RECIPIENTS = 'recipients';
}
