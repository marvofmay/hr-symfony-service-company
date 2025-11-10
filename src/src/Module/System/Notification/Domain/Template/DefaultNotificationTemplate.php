<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Template;

use App\Module\System\Notification\Domain\Interface\Template\NotificationTemplateInterface;

class DefaultNotificationTemplate implements NotificationTemplateInterface
{
    public function isDefault(): bool
    {
        return true;
    }

    public function getTitle(): string
    {
        return 'default title';
    }

    public function getContent(): string
    {
        return 'default content';
    }
}