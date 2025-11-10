<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Application\Event;

use App\Module\System\Application\Event\Event;
use App\Module\System\Notification\Domain\Entity\NotificationChannelSetting;

class NotificationChannelSettingsEvent extends Event
{
    public function getEntityClass(): string
    {
        return NotificationChannelSetting::class;
    }
}
