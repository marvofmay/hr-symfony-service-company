<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Interface\Template;

use App\Module\System\Notification\Domain\Interface\Channel\NotificationChannelInterface;
use App\Module\System\Notification\Domain\Interface\Event\NotificationEventInterface;

interface NotificationTemplateSettingCreatorInterface
{
    public function create(
        NotificationEventInterface $event,
        NotificationChannelInterface $channel,
        string $title,
        string $content,
        bool $isDefault,
        bool $isActive,
    ): void;
}