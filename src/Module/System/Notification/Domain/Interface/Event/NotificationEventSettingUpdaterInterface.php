<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Interface\Event;

use App\Module\System\Notification\Domain\Entity\NotificationEventSetting;

interface NotificationEventSettingUpdaterInterface
{
    public function update(NotificationEventSetting $notificationEventSetting, bool $enabled = true): void;
}
