<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Service\Event;

use App\Module\System\Notification\Domain\Entity\NotificationEventSetting;
use App\Module\System\Notification\Domain\Interface\Event\NotificationEventSettingUpdaterInterface;
use App\Module\System\Notification\Domain\Interface\Event\NotificationEventSettingWriterInterface;

final readonly class NotificationEventSettingUpdater implements NotificationEventSettingUpdaterInterface
{
    public function __construct(private NotificationEventSettingWriterInterface $notificationEventSettingWriter)
    {
    }

    public function update(NotificationEventSetting $notificationEventSetting, bool $enabled = true): void
    {
        if ($enabled) {
            $notificationEventSetting->enable();
        } else {
            $notificationEventSetting->disable();
        }

        $this->notificationEventSettingWriter->save($notificationEventSetting);
    }
}
