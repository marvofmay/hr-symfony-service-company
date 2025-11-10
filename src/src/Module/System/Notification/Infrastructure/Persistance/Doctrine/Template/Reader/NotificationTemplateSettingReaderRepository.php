<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Infrastructure\Persistance\Doctrine\Template\Reader;

use App\Module\System\Notification\Domain\Entity\NotificationTemplateSetting;
use App\Module\System\Notification\Domain\Interface\Template\NotificationTemplateSettingReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NotificationTemplateSetting>
 */
class NotificationTemplateSettingReaderRepository extends ServiceEntityRepository implements NotificationTemplateSettingReaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationTemplateSetting::class);
    }

    public function getAll(): Collection
    {
        return new ArrayCollection($this->findAll());
    }
}