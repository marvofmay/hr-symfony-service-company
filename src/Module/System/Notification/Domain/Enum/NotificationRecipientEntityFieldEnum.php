<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Enum;

enum NotificationRecipientEntityFieldEnum: string
{
    case UUID = 'uuid';
    case RECEIVED_AT = 'receivedAt';
    case READ_AT = 'readAt';
}
