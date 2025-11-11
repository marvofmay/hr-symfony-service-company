<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Interface\Template;

use App\Module\System\Notification\Domain\Entity\NotificationTemplateSetting;
use App\Module\System\Notification\Domain\Interface\Channel\NotificationChannelInterface;
use App\Module\System\Notification\Domain\Interface\Event\NotificationEventInterface;

interface NotificationTemplateSettingUpdaterInterface
{
    public function update(
        NotificationTemplateSetting $notificationTemplateSetting,
        NotificationEventInterface $event,
        NotificationChannelInterface $channel,
        string $title,
        string $content,
        bool $isDefault,
        bool $isActive
    ): void;
}