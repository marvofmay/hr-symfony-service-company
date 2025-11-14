<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Channel;

use App\Module\System\Notification\Domain\Interface\Channel\NotificationChannelInterface;

class SmsNotificationChannel implements NotificationChannelInterface
{
    public function getCode(): string
    {
        return 'sms';
    }

    public function getLabel(): string
    {
        return 'notification.channel.sms';
    }
}