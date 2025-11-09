<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\Notification;

use App\Module\System\Domain\Entity\NotificationChannelSetting;

interface NotificationChannelSettingUpdaterInterface
{
    public function update(NotificationChannelSetting $notificationChannelSetting, bool $enabled = true): void;
}