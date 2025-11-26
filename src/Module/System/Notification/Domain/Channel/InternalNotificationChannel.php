<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Channel;

use App\Module\System\Notification\Domain\Abstract\NotificationChannelAbstract;

final class InternalNotificationChannel  extends NotificationChannelAbstract
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