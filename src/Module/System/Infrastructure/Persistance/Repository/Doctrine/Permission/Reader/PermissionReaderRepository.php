<?php

declare(strict_types=1);

namespace App\Module\System\Infrastructure\Persistance\Repository\Doctrine\Permission\Reader;

use App\Module\System\Domain\Entity\Permission;
use App\Module\System\Domain\Enum\Permission\PermissionEntityFieldEnum;
use App\Module\System\Domain\Interface\Permission\PermissionReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

class PermissionReaderRepository extends ServiceEntityRepository implements PermissionReaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Permission::class);
    }

    public function getPermissions(): Collection
    {
        return new ArrayCollection($this->findAll());
    }

    public function getPermissionByUuid(string $uuid): ?Permission
    {
        return $this->findOneBy([PermissionEntityFieldEnum::UUID->value => $uuid]);
    }

    public function getPermissionByName(string $name): ?Permission
    {
        return $this->findOneBy([
            PermissionEntityFieldEnum::NAME->value => $name,
            PermissionEntityFieldEnum::ACTIVE->value => true
        ]);
    }

    public function isPermissionWithUUIDExists(string $uuid): bool
    {
        return null !== $this->findOneBy([PermissionEntityFieldEnum::UUID->value => $uuid]);
    }

    public function isPermissionWithNameExists(string $name): bool
    {
        return null !== $this->findOneBy([PermissionEntityFieldEnum::NAME->value => $name]);
    }

    public function isPermissionActive(string $uuid): bool
    {
        return null !== $this->findOneBy([
                PermissionEntityFieldEnum::UUID->value => $uuid,
                PermissionEntityFieldEnum::ACTIVE->value => true
        ]);
    }

    public function getPermissionsByUUIDs(array $uuids): Collection
    {
        if (empty($uuids)) {
            return new ArrayCollection();
        }

        return new ArrayCollection(
            $this->createQueryBuilder(Permission::ALIAS)
                ->where(sprintf('%s.%s IN (:uuids)', Permission::ALIAS, PermissionEntityFieldEnum::UUID->value))
                ->setParameter('uuids', $uuids)
                ->getQuery()
                ->getResult()
        );
    }
}
