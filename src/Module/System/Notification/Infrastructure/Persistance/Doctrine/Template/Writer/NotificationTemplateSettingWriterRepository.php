<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Infrastructure\Persistance\Doctrine\Template\Writer;

use App\Common\Domain\Enum\DeleteTypeEnum;
use App\Module\System\Notification\Domain\Entity\NotificationTemplateSetting;
use App\Module\System\Notification\Domain\Enum\NotificationTemplateSettingEntityFieldEnum;
use App\Module\System\Notification\Domain\Interface\Template\NotificationTemplateSettingWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NotificationTemplateSetting>
 */
class NotificationTemplateSettingWriterRepository extends ServiceEntityRepository implements NotificationTemplateSettingWriterInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationTemplateSetting::class);
    }

    public function save(NotificationTemplateSetting $notificationTemplateSetting): void
    {
        $this->getEntityManager()->persist($notificationTemplateSetting);
        $this->getEntityManager()->flush();
    }

    public function delete(NotificationTemplateSetting $notificationTemplateSetting, DeleteTypeEnum $deleteTypeEnum = DeleteTypeEnum::SOFT_DELETE): void
    {
        if (DeleteTypeEnum::HARD_DELETE === $deleteTypeEnum) {
            $this->getEntityManager()->createQueryBuilder()
                ->delete(NotificationTemplateSetting::class, NotificationTemplateSetting::ALIAS)
                ->where(NotificationTemplateSetting::ALIAS.'.'. NotificationTemplateSettingEntityFieldEnum::UUID->value . '= :uuid')
                ->setParameter('uuid', $notificationTemplateSetting->getUUID())
                ->getQuery()
                ->execute();
        } else {
            $this->getEntityManager()->remove($notificationTemplateSetting);
            $this->getEntityManager()->flush();
        }
    }
}
