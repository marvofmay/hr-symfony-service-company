<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Infrastructure\Persistance\Doctrine\Event\Writer;

use App\Common\Domain\Enum\DeleteTypeEnum;
use App\Module\System\Notification\Domain\Entity\NotificationEventSetting;
use App\Module\System\Notification\Domain\Enum\NotificationEventSettingEntityFieldEnum;
use App\Module\System\Notification\Domain\Interface\Event\NotificationEventSettingWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NotificationEventSetting>
 */
class NotificationEventSettingWriterRepository extends ServiceEntityRepository implements NotificationEventSettingWriterInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationEventSetting::class);
    }

    public function save(NotificationEventSetting $notificationEventSetting): void
    {
        $this->getEntityManager()->persist($notificationEventSetting);
        $this->getEntityManager()->flush();
    }

    public function delete(NotificationEventSetting $notificationEventSetting, DeleteTypeEnum $deleteTypeEnum = DeleteTypeEnum::SOFT_DELETE): void
    {
        if (DeleteTypeEnum::HARD_DELETE === $deleteTypeEnum) {
            $this->getEntityManager()->createQueryBuilder()
                ->delete(NotificationEventSetting::class, NotificationEventSetting::ALIAS)
                ->where(NotificationEventSetting::ALIAS.'.'. NotificationEventSettingEntityFieldEnum::EVENT_NAME->value . '= :eventName')
                ->setParameter('eventName', $notificationEventSetting->getEventName())
                ->getQuery()
                ->execute();
        } else {
            $this->getEntityManager()->remove($notificationEventSetting);
            $this->getEntityManager()->flush();
        }
    }
}