<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Interface;

use App\Module\System\Notification\Domain\Entity\NotificationChannelSetting;

interface NotificationChannelSettingUpdaterInterface
{
    public function update(NotificationChannelSetting $notificationChannelSetting, bool $enabled = true): void;
}