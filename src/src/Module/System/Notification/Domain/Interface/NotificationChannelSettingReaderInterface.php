<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Interface;

use App\Module\System\Notification\Domain\Entity\NotificationChannelSetting;
use Doctrine\Common\Collections\Collection;

interface NotificationChannelSettingReaderInterface
{
    public function getByChannelCode(NotificationChannelInterface $channel): ?NotificationChannelSetting;
    public function getAll(): Collection;
}
