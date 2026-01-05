<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Service\Template;

use App\Module\System\Notification\Domain\Entity\NotificationTemplateSetting;
use App\Module\System\Notification\Domain\Interface\Template\NotificationTemplateSettingUpdaterInterface;
use App\Module\System\Notification\Domain\Interface\Template\NotificationTemplateSettingWriterInterface;

final readonly class NotificationTemplateSettingUpdater implements NotificationTemplateSettingUpdaterInterface
{
    public function __construct(private NotificationTemplateSettingWriterInterface $notificationTemplateSettingWriter)
    {
    }

    public function update(
        NotificationTemplateSetting $notificationTemplateSetting,
        string $title,
        string $content,
        bool $searchDefault,
        bool $markAsActive
    ): void {
        $notificationTemplateSetting->changeTitle($title);
        $notificationTemplateSetting->changeContent($content);
        $markAsActive ? $notificationTemplateSetting->activate() : $notificationTemplateSetting->deactivate();

        $this->notificationTemplateSettingWriter->save($notificationTemplateSetting);
    }
}
