<?php

declare(strict_types=1);

namespace App\Module\System\Application\Transformer\Notification;

use App\Common\Domain\Interface\DataTransformerInterface;
use App\Module\Company\Domain\Enum\TimeStampableEntityFieldEnum;
use App\Module\System\Application\QueryHandler\Notification\ListNotificationChannelSettingQueryHandler;
use App\Module\System\Domain\Entity\NotificationChannelSetting;
use App\Module\System\Domain\Enum\Notification\NotificationChannelSettingEntityFieldEnum;

class NotificationChannelSettingDataTransformer implements DataTransformerInterface
{

    public static function supports(): string
    {
        return ListNotificationChannelSettingQueryHandler::class;
    }

    public function transformToArray(NotificationChannelSetting $notificationChannelSetting): array
    {
        return [
            NotificationChannelSettingEntityFieldEnum::CHANNEL->value => $notificationChannelSetting->getChannel(),
            NotificationChannelSettingEntityFieldEnum::ENABLED->value => $notificationChannelSetting->isEnabled(),
            TimeStampableEntityFieldEnum::CREATED_AT->value => $notificationChannelSetting->createdAt->format('Y-m-d H:i:s'),
            TimeStampableEntityFieldEnum::UPDATED_AT->value => $notificationChannelSetting->updatedAt?->format('Y-m-d H:i:s'),
            TimeStampableEntityFieldEnum::DELETED_AT->value => $notificationChannelSetting->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }



}
