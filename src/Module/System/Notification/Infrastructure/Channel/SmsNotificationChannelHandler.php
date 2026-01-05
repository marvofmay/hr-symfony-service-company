<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Infrastructure\Channel;

use App\Module\System\Notification\Domain\Channel\SmsNotificationChannel;
use App\Module\System\Notification\Domain\Entity\NotificationChannelSetting;
use App\Module\System\Notification\Domain\Entity\NotificationEventSetting;
use App\Module\System\Notification\Domain\Interface\Channel\NotificationChannelHandlerInterface;

final readonly class SmsNotificationChannelHandler implements NotificationChannelHandlerInterface
{
    public function supports(NotificationChannelSetting $channel): bool
    {
        return $channel->getChannelCode() === SmsNotificationChannel::getChanelCode();
    }

    public function send(NotificationEventSetting $event, array $recipientUUIDs, string $title, string $content, array $payload = []): void
    {
        // dispatch new SendSmsCommand(...)
    }
}
