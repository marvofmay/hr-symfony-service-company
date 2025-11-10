<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Service\Channel;

use App\Module\System\Notification\Domain\Entity\NotificationChannelSetting;
use App\Module\System\Notification\Domain\Interface\Channel\NotificationChannelInterface;
use App\Module\System\Notification\Domain\Interface\Channel\NotificationChannelSettingCreatorInterface;
use App\Module\System\Notification\Domain\Interface\Channel\NotificationChannelSettingWriterInterface;

final readonly class NotificationChannelSettingCreator implements NotificationChannelSettingCreatorInterface
{
    public function __construct(private NotificationChannelSettingWriterInterface $notificationChannelSettingWriter)
    {
    }

    public function create(NotificationChannelInterface $channel, bool $enabled = true): void
    {
        $notificationChannelSetting = NotificationChannelSetting::create($channel, $enabled);
        $this->notificationChannelSettingWriter->save($notificationChannelSetting);
    }
}
