<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Application\Transformer;

use App\Common\Domain\Interface\DataTransformerInterface;
use App\Module\Company\Domain\Enum\TimeStampableEntityFieldEnum;
use App\Module\System\Notification\Application\QueryHandler\Template\ListNotificationTemplateSettingQueryHandler;
use App\Module\System\Notification\Domain\Entity\NotificationTemplateSetting;
use App\Module\System\Notification\Domain\Enum\NotificationTemplateSettingEntityFieldEnum;

class NotificationTemplateSettingDataTransformer implements DataTransformerInterface
{

    public static function supports(): string
    {
        return ListNotificationTemplateSettingQueryHandler::class;
    }

    public function transformToArray(NotificationTemplateSetting $notificationTemplateSetting): array
    {
        return [
            NotificationTemplateSettingEntityFieldEnum::EVENT_NANE->value => $notificationTemplateSetting->getEventName(),
            NotificationTemplateSettingEntityFieldEnum::CHANNEL_CODE->value => $notificationTemplateSetting->getChannelCode(),
            NotificationTemplateSettingEntityFieldEnum::TITLE->value => $notificationTemplateSetting->getTitle(),
            NotificationTemplateSettingEntityFieldEnum::CONTENT->value => $notificationTemplateSetting->getContent(),
            NotificationTemplateSettingEntityFieldEnum::IS_DEFAULT->value => $notificationTemplateSetting->isDefault(),
            NotificationTemplateSettingEntityFieldEnum::IS_ACTIVE->value => $notificationTemplateSetting->isActive(),
            TimeStampableEntityFieldEnum::CREATED_AT->value => $notificationTemplateSetting->createdAt->format('Y-m-d H:i:s'),
            TimeStampableEntityFieldEnum::UPDATED_AT->value => $notificationTemplateSetting->updatedAt?->format('Y-m-d H:i:s'),
            TimeStampableEntityFieldEnum::DELETED_AT->value => $notificationTemplateSetting->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }



}
