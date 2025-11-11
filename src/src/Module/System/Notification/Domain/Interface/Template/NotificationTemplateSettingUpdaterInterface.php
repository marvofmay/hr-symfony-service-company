<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Interface\Template;


use App\Module\System\Notification\Domain\Entity\NotificationTemplateSetting;

interface NotificationTemplateSettingUpdaterInterface
{
    public function update(
        NotificationTemplateSetting $notificationTemplateSetting,
        string $title,
        string $content,
        bool $searchDefault,
        bool $markAsActive
    ): void;
}