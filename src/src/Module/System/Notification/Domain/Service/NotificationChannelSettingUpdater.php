<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Service;

use App\Module\System\Notification\Domain\Entity\NotificationChannelSetting;
use App\Module\System\Notification\Domain\Interface\NotificationChannelSettingUpdaterInterface;
use App\Module\System\Notification\Domain\Interface\NotificationChannelSettingWriterInterface;

final readonly class NotificationChannelSettingUpdater implements NotificationChannelSettingUpdaterInterface
{
    public function __construct(private NotificationChannelSettingWriterInterface $notificationChannelSettingWriter)
    {
    }

    public function update(NotificationChannelSetting $notificationChannelSetting, bool $enabled = true): void
    {
        if ($enabled) {
            $notificationChannelSetting->enable();
        } else {
            $notificationChannelSetting->disable();
        }

        $this->notificationChannelSettingWriter->save($notificationChannelSetting);
    }
}
