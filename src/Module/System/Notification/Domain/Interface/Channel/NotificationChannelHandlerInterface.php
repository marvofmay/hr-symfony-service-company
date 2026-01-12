<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Interface\Channel;

use App\Module\System\Notification\Domain\Entity\NotificationChannelSetting;
use App\Module\System\Notification\Domain\Entity\NotificationEventSetting;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'app.notification.channel.handler')]
interface NotificationChannelHandlerInterface
{
    public function supports(NotificationChannelSetting $channel): bool;

    public function send(NotificationEventSetting $event, array $recipients, string $title, string $content, array $payload = []): void;
}
