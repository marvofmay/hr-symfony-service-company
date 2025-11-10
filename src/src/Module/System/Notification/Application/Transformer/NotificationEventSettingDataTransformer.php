<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Application\Transformer;

use App\Common\Domain\Interface\DataTransformerInterface;
use App\Module\Company\Domain\Enum\TimeStampableEntityFieldEnum;
use App\Module\System\Notification\Application\QueryHandler\Event\ListNotificationEventSettingQueryHandler;
use App\Module\System\Notification\Domain\Entity\NotificationEventSetting;
use App\Module\System\Notification\Domain\Enum\NotificationEventSettingEntityFieldEnum;

class NotificationEventSettingDataTransformer implements DataTransformerInterface
{

    public static function supports(): string
    {
        return ListNotificationEventSettingQueryHandler::class;
    }

    public function transformToArray(NotificationEventSetting $notificationEventSetting): array
    {
        return [
            NotificationEventSettingEntityFieldEnum::EVENT_NAME->value => $notificationEventSetting->getEventName(),
            NotificationEventSettingEntityFieldEnum::ENABLED->value => $notificationEventSetting->isEnabled(),
            TimeStampableEntityFieldEnum::CREATED_AT->value => $notificationEventSetting->createdAt->format('Y-m-d H:i:s'),
            TimeStampableEntityFieldEnum::UPDATED_AT->value => $notificationEventSetting->updatedAt?->format('Y-m-d H:i:s'),
            TimeStampableEntityFieldEnum::DELETED_AT->value => $notificationEventSetting->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }



}
