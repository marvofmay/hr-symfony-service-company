<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Channel;

use App\Module\System\Notification\Domain\Abstract\NotificationChannelAbstract;

class SmsNotificationChannel extends NotificationChannelAbstract
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
