<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Interface\Event;

use App\Common\Domain\Enum\DeleteTypeEnum;
use App\Module\System\Notification\Domain\Entity\NotificationEventSetting;

interface NotificationEventSettingWriterInterface
{
    public function save(NotificationEventSetting $notificationEventSetting): void;
    public function delete(NotificationEventSetting $notificationEventSetting, DeleteTypeEnum $deleteTypeEnum = DeleteTypeEnum::SOFT_DELETE): void;
}
