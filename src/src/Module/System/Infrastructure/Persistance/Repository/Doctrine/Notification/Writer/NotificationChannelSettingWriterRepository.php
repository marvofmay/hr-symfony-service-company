<?php

declare(strict_types=1);

namespace App\Module\System\Infrastructure\Persistance\Repository\Doctrine\Notification\Writer;

use App\Common\Domain\Enum\DeleteTypeEnum;
use App\Module\System\Domain\Entity\NotificationChannelSetting;
use App\Module\System\Domain\Enum\Notification\NotificationChannelSettingEntityFieldEnum;
use App\Module\System\Domain\Interface\Notification\NotificationChannelSettingWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NotificationChannelSetting>
 */
class NotificationChannelSettingWriterRepository extends ServiceEntityRepository implements NotificationChannelSettingWriterInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationChannelSetting::class);
    }

    public function save(NotificationChannelSetting $notificationChannelSetting): void
    {
        $this->getEntityManager()->persist($notificationChannelSetting);
        $this->getEntityManager()->flush();
    }

    public function delete(NotificationChannelSetting $notificationChannelSetting, DeleteTypeEnum $deleteTypeEnum = DeleteTypeEnum::SOFT_DELETE): void
    {
        if (DeleteTypeEnum::HARD_DELETE === $deleteTypeEnum) {
            $this->getEntityManager()->createQueryBuilder()
                ->delete(NotificationChannelSetting::class, NotificationChannelSetting::ALIAS)
                ->where(NotificationChannelSetting::ALIAS.'.'. NotificationChannelSettingEntityFieldEnum::CHANNEL->value . '= :channelName')
                ->setParameter('channelName', $notificationChannelSetting->getChannel())
                ->getQuery()
                ->execute();
        } else {
            $this->getEntityManager()->remove($notificationChannelSetting);
            $this->getEntityManager()->flush();
        }
    }
}