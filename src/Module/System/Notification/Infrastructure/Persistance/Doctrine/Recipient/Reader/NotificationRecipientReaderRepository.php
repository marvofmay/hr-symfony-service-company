<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Infrastructure\Persistance\Doctrine\Recipient\Reader;

use App\Module\Note\Domain\Entity\Note;
use App\Module\Note\Domain\Enum\NoteEntityFieldEnum;
use App\Module\Note\Domain\Enum\NoteEntityRelationFieldEnum;
use App\Module\Note\Domain\Interface\NoteReaderInterface;
use App\Module\System\Notification\Domain\Channel\InternalNotificationChannel;
use App\Module\System\Notification\Domain\Entity\NotificationRecipient;
use App\Module\System\Notification\Domain\Interface\Recipient\NotificationRecipientReaderInterface;
use App\Module\System\Notification\Infrastructure\Channel\SmsNotificationChannelHandler;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<NotificationRecipient>
 */
final class NotificationRecipientReaderRepository extends ServiceEntityRepository implements NotificationRecipientReaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationRecipient::class);
    }

    public function countUnreadNotificationMessagesForUser(string $userUUID): int
    {
        return (int) $this->createQueryBuilder(NotificationRecipient::ALIAS)
            ->select('COUNT(' . NotificationRecipient::ALIAS . '.uuid)')
            ->innerJoin(
                NotificationRecipient::ALIAS . '.message',
                'nm'
            )
            ->innerJoin(
                'nm.channel',
                'ncs'
            )
            ->andWhere(NotificationRecipient::ALIAS . '.user = :userUUID')
            ->andWhere(NotificationRecipient::ALIAS . '.readAt IS NULL')
            ->andWhere('ncs.channelCode = :channelCode')
            ->setParameter('userUUID', $userUUID)
            ->setParameter('channelCode', InternalNotificationChannel::getChanelCode())
            ->getQuery()
            ->getSingleScalarResult();
    }
}
