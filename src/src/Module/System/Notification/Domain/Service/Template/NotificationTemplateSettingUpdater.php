<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Service\Template;

use App\Module\System\Notification\Domain\Entity\NotificationTemplateSetting;
use App\Module\System\Notification\Domain\Interface\Channel\NotificationChannelInterface;
use App\Module\System\Notification\Domain\Interface\Event\NotificationEventInterface;
use App\Module\System\Notification\Domain\Interface\Template\NotificationTemplateSettingUpdaterInterface;
use App\Module\System\Notification\Domain\Interface\Template\NotificationTemplateSettingWriterInterface;

final readonly class NotificationTemplateSettingUpdater implements NotificationTemplateSettingUpdaterInterface
{
    public function __construct(private NotificationTemplateSettingWriterInterface $notificationTemplateSettingWriter)
    {
    }

    public function update(
        NotificationTemplateSetting $notificationTemplateSetting,
        NotificationEventInterface $event,
        NotificationChannelInterface $channel,
        string $title,
        string $content,
        bool $isDefault,
        bool $isActive
    ): void
    {
        //$notificationTemplateSetting->updateTitle($title);
        //$notificationTemplateSetting->updateContent($channel);

        $this->notificationTemplateSettingWriter->save($notificationTemplateSetting);
    }
}
