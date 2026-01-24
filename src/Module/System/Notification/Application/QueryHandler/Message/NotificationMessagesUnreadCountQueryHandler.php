<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Application\QueryHandler\Message;

use App\Module\System\Notification\Application\Query\Message\NotificationMessagesUnreadCountQuery;
use App\Module\System\Notification\Domain\Interface\Recipient\NotificationRecipientReaderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
readonly class NotificationMessagesUnreadCountQueryHandler
{
    public function __construct(
        private NotificationRecipientReaderInterface $notificationRecipientRepository,
        private Security $security,
    ) {
    }

    public function __invoke(NotificationMessagesUnreadCountQuery $query): int
    {
        $user = $this->security->getUser();
        if (!$user) {
            return 0;
        }

        return $this->notificationRecipientRepository->countUnreadNotificationMessagesForUser($user->getUuid()->toString());
    }
}
