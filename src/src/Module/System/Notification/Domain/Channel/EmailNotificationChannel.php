<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Channel;

use App\Module\System\Notification\Domain\Interface\NotificationChannelInterface;

class EmailNotificationChannel implements NotificationChannelInterface
{
    public function getCode(): string
    {
        return 'email';
    }

    public function getLabel(): string
    {
        return 'notification.channel.email';
    }
}