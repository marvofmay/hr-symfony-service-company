<?php

declare(strict_types=1);

namespace App\Module\System\Infrastructure\Persistance\Repository\Doctrine\Permission\Reader;

use App\Module\System\Domain\Entity\Permission;
use App\Module\System\Domain\Interface\Permission\PermissionReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PermissionReaderRepository extends ServiceEntityRepository implements PermissionReaderInterface
{
    public function __construct(ManagerRegistry $registry,)
    {
        parent::__construct($registry, Permission::class);
    }
    public function getPermissionByUuid(string $uuid): ?Permission
    {
        return $this->findOneBy([Permission::COLUMN_UUID => $uuid]);
    }

    public function getPermissionByName(string $name): ?Permission
    {
        return $this->findOneBy([Permission::COLUMN_NAME => $name, Permission::COLUMN_ACTIVE => true]);
    }

    public function isPermissionWithUUIDExists(string $uuid): bool
    {
        return null !== $this->findOneBy([Permission::COLUMN_UUID => $uuid]);
    }

    public function isPermissionWithNameExists(string $name): bool
    {
        return null !== $this->findOneBy([Permission::COLUMN_NAME => $name]);
    }

    public function isPermissionActive(string $uuid): bool
    {
        return null !== $this->findOneBy([Permission::COLUMN_UUID => $uuid, Permission::COLUMN_ACTIVE => true]);
    }
}