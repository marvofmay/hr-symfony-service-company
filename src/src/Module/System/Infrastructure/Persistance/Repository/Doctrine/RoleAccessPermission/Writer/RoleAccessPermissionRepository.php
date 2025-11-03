<?php

declare(strict_types=1);

namespace App\Module\System\Infrastructure\Persistance\Repository\Doctrine\RoleAccessPermission\Writer;

use App\Common\Domain\Enum\DeleteTypeEnum;
use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Domain\Entity\Access;
use App\Module\System\Domain\Entity\Permission;
use App\Module\System\Domain\Entity\RoleAccessPermission;
use App\Module\System\Domain\Enum\RoleAccessPermission\RoleAccessPermissionEntityRelationFieldEnum;
use App\Module\System\Domain\Interface\RoleAccessPermission\RoleAccessPermissionWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RoleAccessPermissionRepository extends ServiceEntityRepository implements RoleAccessPermissionWriterInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoleAccessPermission::class);
    }

    public function deleteRoleAccessPermissionsInDB(Role $role, Access $access, Permission $permission, DeleteTypeEnum $deleteTypeEnum = DeleteTypeEnum::SOFT_DELETE): void
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        if (DeleteTypeEnum::HARD_DELETE === $deleteTypeEnum) {
            $qb->delete(RoleAccessPermission::class, RoleAccessPermission::ALIAS)
                ->where(RoleAccessPermission::ALIAS . '.' . RoleAccessPermissionEntityRelationFieldEnum::ROLE->value . ' = :roleUUID')
                ->andWhere(RoleAccessPermission::ALIAS . '.' . RoleAccessPermissionEntityRelationFieldEnum::ACCESS->value . ' = :accessUUID')
                ->andWhere(RoleAccessPermission::ALIAS . '.' . RoleAccessPermissionEntityRelationFieldEnum::PERMISSION->value . ' = :permissionUUID')
                ->setParameter('roleUUID', $role->getUUID())
                ->setParameter('accessUUID', $access->getUUID())
                ->setParameter('permissionUUID', $permission->getUUID())
                ->getQuery()
                ->execute();
        } else {
            $qb->update(RoleAccessPermission::class, RoleAccessPermission::ALIAS)
                ->set(RoleAccessPermission::ALIAS . '.deletedAt', ':now')
                ->where(RoleAccessPermission::ALIAS . '.' . RoleAccessPermissionEntityRelationFieldEnum::ROLE->value . ' = :roleUUID')
                ->andWhere(RoleAccessPermission::ALIAS . '.' . RoleAccessPermissionEntityRelationFieldEnum::ACCESS->value . ' = :accessUUID')
                ->andWhere(RoleAccessPermission::ALIAS . '.' . RoleAccessPermissionEntityRelationFieldEnum::PERMISSION->value . ' = :permissionUUID')
                ->setParameter('roleUUID', $role->getUUID())
                ->setParameter('accessUUID', $access->getUUID())
                ->setParameter('permissionUUID', $permission->getUUID())
                ->setParameter('now', new \DateTimeImmutable())
                ->getQuery()
                ->execute();
        }
    }

    public function deleteRoleAccessPermissionsByRoleAndAccessInDB(Role $role, Access $access, DeleteTypeEnum $deleteTypeEnum = DeleteTypeEnum::SOFT_DELETE): void
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        if (DeleteTypeEnum::HARD_DELETE === $deleteTypeEnum) {
            $qb->delete(RoleAccessPermission::class, RoleAccessPermission::ALIAS)
                ->where(RoleAccessPermission::ALIAS . '.' . RoleAccessPermissionEntityRelationFieldEnum::ROLE->value . ' = :roleUUID')
                ->andWhere(RoleAccessPermission::ALIAS . '.' . RoleAccessPermissionEntityRelationFieldEnum::ACCESS->value . ' = :accessUUID')
                ->setParameter('roleUUID', $role->getUUID())
                ->setParameter('accessUUID', $access->getUUID())
                ->getQuery()
                ->execute();
        } else {
            $qb->update(RoleAccessPermission::class, RoleAccessPermission::ALIAS)
                ->set(RoleAccessPermission::ALIAS . '.deletedAt', ':now')
                ->where(RoleAccessPermission::ALIAS . '.' . RoleAccessPermissionEntityRelationFieldEnum::ROLE->value . ' = :roleUUID')
                ->andWhere(RoleAccessPermission::ALIAS . '.' . RoleAccessPermissionEntityRelationFieldEnum::ACCESS->value . ' = :accessUUID')
                ->setParameter('roleUUID', $role->getUUID())
                ->setParameter('accessUUID', $access->getUUID())
                ->setParameter('now', new \DateTimeImmutable())
                ->getQuery()
                ->execute();
        }
    }
}
