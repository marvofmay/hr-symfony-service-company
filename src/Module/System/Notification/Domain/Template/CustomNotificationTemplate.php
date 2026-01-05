<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Template;

use App\Module\System\Notification\Domain\Interface\Template\NotificationTemplateInterface;

class CustomNotificationTemplate implements NotificationTemplateInterface
{
    public function isDefault(): bool
    {
        return false;
    }

    public function getTitle(): string
    {
        return 'custom title';
    }

    public function getContent(): string
    {
        return 'custom content';
    }
}
