<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Application\Event\Message;

use App\Module\System\Application\Event\Event;
use App\Module\System\Notification\Domain\Entity\NotificationMessage;

class NotificationMessageEvent extends Event
{
    public function getEntityClass(): string
    {
        return NotificationMessage::class;
    }
}
