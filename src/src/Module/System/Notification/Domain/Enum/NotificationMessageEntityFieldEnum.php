<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Enum;

enum NotificationMessageEntityFieldEnum: string
{
    case UUID = 'uuid';
    case TITLE = 'title';
    case CONTENT = 'content';
    case SENT_AT = 'sentAt';
}
