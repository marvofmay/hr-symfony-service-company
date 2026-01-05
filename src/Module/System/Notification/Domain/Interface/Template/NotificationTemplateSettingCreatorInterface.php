<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Interface\Template;

use App\Module\System\Notification\Domain\Entity\NotificationChannelSetting;
use App\Module\System\Notification\Domain\Entity\NotificationEventSetting;

interface NotificationTemplateSettingCreatorInterface
{
    public function create(
        NotificationEventSetting $event,
        NotificationChannelSetting $channel,
        string $title,
        string $content,
        bool $isDefault,
        bool $isActive,
    ): void;
}
