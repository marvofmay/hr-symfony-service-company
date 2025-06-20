<?php

declare(strict_types=1);

namespace App\Module\System\Infrastructure\Persistance\Repository\Doctrine\Access\Reader;

use App\Module\System\Domain\Entity\Access;
use App\Module\System\Domain\Entity\Module;
use App\Module\System\Domain\Interface\Access\AccessReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

class AccessReaderRepository extends ServiceEntityRepository implements AccessReaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Access::class);
    }

    public function getAccessByUUID(string $uuid): ?Access
    {
        return $this->findOneBy([Access::COLUMN_UUID => $uuid]);
    }

    public function getAccessesByUUID(array $uuids): Collection
    {
        $results = $this->findBy([Access::COLUMN_UUID => $uuids]);

        return new ArrayCollection($results);
    }

    public function getAccessByNameAndModuleUUID(string $name, Module $module): ?Access
    {
        return $this->findOneBy([Access::COLUMN_NAME => $name, Access::RELATION_MODULE => $module]);
    }

    public function isAccessWithUUIDExists(string $uuid): bool
    {
        return null !== $this->findOneBy([Access::COLUMN_UUID => $uuid]);
    }

    public function isAccessActive(string $uuid): bool
    {
        return null !== $this->findOneBy([Access::COLUMN_UUID => $uuid, Access::COLUMN_ACTIVE => true]);
    }
}
