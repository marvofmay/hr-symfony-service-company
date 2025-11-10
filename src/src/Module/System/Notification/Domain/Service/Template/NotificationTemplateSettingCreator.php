<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Service\Template;

use App\Module\System\Notification\Domain\Entity\NotificationTemplateSetting;
use App\Module\System\Notification\Domain\Interface\Channel\NotificationChannelInterface;
use App\Module\System\Notification\Domain\Interface\Event\NotificationEventInterface;
use App\Module\System\Notification\Domain\Interface\Template\NotificationTemplateSettingCreatorInterface;
use App\Module\System\Notification\Domain\Interface\Template\NotificationTemplateSettingWriterInterface;

final readonly class NotificationTemplateSettingCreator implements NotificationTemplateSettingCreatorInterface
{
    public function __construct(private NotificationTemplateSettingWriterInterface $notificationTemplateSettingWriter)
    {
    }

    public function create(NotificationEventInterface $event, NotificationChannelInterface $channel, string $title, string $content, bool $isDefault): void
    {
        $notificationTemplateSetting = NotificationTemplateSetting::create($event, $channel, $title, $content, $isDefault);
        $this->notificationTemplateSettingWriter->save($notificationTemplateSetting);
    }
}
