<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\Notification;

use App\Module\System\Domain\Entity\NotificationChannelSetting;
use App\Module\System\Domain\Enum\Notification\NotificationChannelEnum;
use Doctrine\Common\Collections\Collection;

interface NotificationChannelSettingReaderInterface
{
    public function getByChannelName(NotificationChannelEnum $channelEnum): ?NotificationChannelSetting;
    public function getAll(): Collection;
}
