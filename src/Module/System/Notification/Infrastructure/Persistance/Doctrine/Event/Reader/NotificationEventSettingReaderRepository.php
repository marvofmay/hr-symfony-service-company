<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Infrastructure\Persistance\Doctrine\Event\Reader;

use App\Module\System\Notification\Domain\Entity\NotificationEventSetting;
use App\Module\System\Notification\Domain\Enum\NotificationEventSettingEntityFieldEnum;
use App\Module\System\Notification\Domain\Interface\Event\NotificationEventInterface;
use App\Module\System\Notification\Domain\Interface\Event\NotificationEventSettingReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NotificationEventSetting>
 */
class NotificationEventSettingReaderRepository extends ServiceEntityRepository implements NotificationEventSettingReaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationEventSetting::class);
    }

    public function getByEventName(NotificationEventInterface $event): ?NotificationEventSetting
    {
        return $this->findOneBy([NotificationEventSettingEntityFieldEnum::EVENT_NAME->value => $event->getName()]);
    }

    public function getAll(): Collection
    {
        return new ArrayCollection($this->findAll());
    }
}
