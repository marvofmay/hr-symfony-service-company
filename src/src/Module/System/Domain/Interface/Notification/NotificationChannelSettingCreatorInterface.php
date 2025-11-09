<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\Notification;

use App\Module\System\Domain\Enum\Notification\NotificationChannelEnum;

interface NotificationChannelSettingCreatorInterface
{
    public function create(NotificationChannelEnum $channelEnum, bool $enabled = true): void;
}