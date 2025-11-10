<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Infrastructure\Persistance\Doctrine\Channel\Reader;

use App\Module\System\Notification\Domain\Entity\NotificationChannelSetting;
use App\Module\System\Notification\Domain\Enum\NotificationChannelSettingEntityFieldEnum;
use App\Module\System\Notification\Domain\Interface\Channel\NotificationChannelInterface;
use App\Module\System\Notification\Domain\Interface\Channel\NotificationChannelSettingReaderInterface;
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

    public function getByChannelCode(NotificationChannelInterface $channel): ?NotificationChannelSetting
    {
        return $this->findOneBy([NotificationChannelSettingEntityFieldEnum::CHANNEL_CODE->value => $channel->getCode()]);
    }

    public function getAll(): Collection
    {
        return new ArrayCollection($this->findAll());
    }
}