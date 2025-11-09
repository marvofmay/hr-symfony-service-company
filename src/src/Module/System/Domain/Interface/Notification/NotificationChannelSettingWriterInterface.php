<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\Notification;

use App\Common\Domain\Enum\DeleteTypeEnum;
use App\Module\System\Domain\Entity\NotificationChannelSetting;

interface NotificationChannelSettingWriterInterface
{
    public function save(NotificationChannelSetting $notificationChannelSetting): void;
    public function delete(NotificationChannelSetting $notificationChannelSetting, DeleteTypeEnum $deleteTypeEnum = DeleteTypeEnum::SOFT_DELETE): void;
}
