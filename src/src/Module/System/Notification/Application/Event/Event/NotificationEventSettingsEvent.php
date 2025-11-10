<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Application\Event\Event;

use App\Module\System\Application\Event\Event;
use App\Module\System\Notification\Domain\Entity\NotificationEventSetting;

class NotificationEventSettingsEvent extends Event
{
    public function getEntityClass(): string
    {
        return NotificationEventSetting::class;
    }
}
