<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Channel;

use App\Module\System\Notification\Domain\Interface\Channel\NotificationChannelInterface;

class InternalNotificationChannel implements NotificationChannelInterface
{
    public function getCode(): string
    {
        return 'internal';
    }

    public function getLabel(): string
    {
        return 'notification.channel.internal';
    }
}