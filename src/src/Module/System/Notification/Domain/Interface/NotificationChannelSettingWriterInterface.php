<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Interface;

use App\Common\Domain\Enum\DeleteTypeEnum;
use App\Module\System\Notification\Domain\Entity\NotificationChannelSetting;

interface NotificationChannelSettingWriterInterface
{
    public function save(NotificationChannelSetting $notificationChannelSetting): void;
    public function delete(NotificationChannelSetting $notificationChannelSetting, DeleteTypeEnum $deleteTypeEnum = DeleteTypeEnum::SOFT_DELETE): void;
}
