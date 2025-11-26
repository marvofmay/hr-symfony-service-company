<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Application\Event\Template;

use App\Module\System\Application\Event\Event;
use App\Module\System\Notification\Domain\Entity\NotificationTemplateSetting;

class NotificationTemplateSettingEvent extends Event
{
    public function getEntityClass(): string
    {
        return NotificationTemplateSetting::class;
    }
}
