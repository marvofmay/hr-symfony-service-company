<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Service\Notification;

use App\Module\System\Domain\Entity\NotificationChannelSetting;
use App\Module\System\Domain\Enum\Notification\NotificationChannelEnum;
use App\Module\System\Domain\Interface\Notification\NotificationChannelSettingCreatorInterface;
use App\Module\System\Domain\Interface\Notification\NotificationChannelSettingWriterInterface;

final readonly class NotificationChannelSettingCreator implements NotificationChannelSettingCreatorInterface
{
    public function __construct(private NotificationChannelSettingWriterInterface $notificationChannelSettingWriter)
    {
    }

    public function create(NotificationChannelEnum $channelEnum, bool $enabled = true): void
    {
        $notificationChannelSetting = NotificationChannelSetting::create($channelEnum, $enabled);
        $this->notificationChannelSettingWriter->save($notificationChannelSetting);
    }
}
