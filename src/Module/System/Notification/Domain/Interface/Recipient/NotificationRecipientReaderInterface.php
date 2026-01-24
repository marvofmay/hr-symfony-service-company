<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Interface\Recipient;

interface NotificationRecipientReaderInterface
{
    public function countUnreadNotificationMessagesForUser(string $userUUID): int;
}
