<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Application\Transformer;

use App\Common\Domain\Enum\TimeStampableEntityFieldEnum;
use App\Common\Domain\Interface\DataTransformerInterface;
use App\Module\System\Notification\Application\QueryHandler\Message\ListNotificationMessagesQueryHandler;
use App\Module\System\Notification\Domain\Entity\NotificationMessage;
use App\Module\System\Notification\Domain\Entity\NotificationRecipient;
use App\Module\System\Notification\Domain\Enum\NotificationMessageEntityFieldEnum;
use App\Module\System\Notification\Domain\Enum\NotificationRecipientEntityFieldEnum;

final class NotificationMessageDataTransformer implements DataTransformerInterface
{
    public static function supports(): string
    {
        return ListNotificationMessagesQueryHandler::class;
    }

    public function transformToArray(NotificationRecipient $notificationRecipient): array
    {
        return [
            NotificationRecipientEntityFieldEnum::READ_AT->value =>
                $notificationRecipient->getReadAt()?->format('Y-m-d H:i:s'),

            TimeStampableEntityFieldEnum::CREATED_AT->value =>
                $notificationRecipient->createdAt->format('Y-m-d H:i:s'),

            TimeStampableEntityFieldEnum::UPDATED_AT->value =>
                $notificationRecipient->updatedAt?->format('Y-m-d H:i:s'),

            TimeStampableEntityFieldEnum::DELETED_AT->value =>
                $notificationRecipient->deletedAt?->format('Y-m-d H:i:s'),

            'message' => $this->transformNotificationMessage(
                $notificationRecipient->getMessage()
            ),
        ];
    }

    private function transformNotificationMessage(?NotificationMessage $message): ?array
    {
        if (!$message) {
            return null;
        }

        return [
            NotificationMessageEntityFieldEnum::TITLE->value =>
                $message->getTitle(),

            NotificationMessageEntityFieldEnum::CONTENT->value =>
                $message->getContent(),

            TimeStampableEntityFieldEnum::CREATED_AT->value =>
                $message->getCreatedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
