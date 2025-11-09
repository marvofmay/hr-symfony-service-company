<?php

declare(strict_types=1);

namespace App\Module\System\Infrastructure\Persistance\Repository\Doctrine\Notification\Reader;

use App\Module\System\Domain\Entity\NotificationChannelSetting;
use App\Module\System\Domain\Enum\Notification\NotificationChannelEnum;
use App\Module\System\Domain\Interface\Notification\NotificationChannelSettingReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NotificationChannelSetting>
 */
class NotificationChannelSettingReaderRepository extends ServiceEntityRepository implements NotificationChannelSettingReaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationChannelSetting::class);
    }

    public function getByChannelName(NotificationChannelEnum $channelEnum): ?NotificationChannelSetting
    {
        return $this->findOneBy(['channel' => $channelEnum]);
    }

    public function getAll(): Collection
    {
        return new ArrayCollection($this->findAll());
    }
}