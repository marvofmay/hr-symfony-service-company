<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Interface\Event;

use App\Module\System\Notification\Domain\Entity\NotificationEventSetting;
use Doctrine\Common\Collections\Collection;

interface NotificationEventSettingReaderInterface
{
    public function getByEventName(NotificationEventInterface $event): ?NotificationEventSetting;
    public function getAll(): Collection;
}
