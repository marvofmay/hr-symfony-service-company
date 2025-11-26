<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Service\Event;

use App\Module\System\Notification\Domain\Entity\NotificationEventSetting;
use App\Module\System\Notification\Domain\Interface\Event\NotificationEventInterface;
use App\Module\System\Notification\Domain\Interface\Event\NotificationEventSettingCreatorInterface;
use App\Module\System\Notification\Domain\Interface\Event\NotificationEventSettingWriterInterface;

final readonly class NotificationEventSettingCreator implements NotificationEventSettingCreatorInterface
{
    public function __construct(private NotificationEventSettingWriterInterface $notificationEventSettingWriter)
    {
    }

    public function create(NotificationEventInterface $event, bool $enabled = true): void
    {
        $notificationEventSetting = NotificationEventSetting::create($event, $enabled);
        $this->notificationEventSettingWriter->save($notificationEventSetting);
    }
}
