<?php

declare(strict_types=1);

namespace App\Module\System\Infrastructure\Persistance\Repository\Doctrine\RoleAccess\Writer;

use App\Common\Domain\Enum\DeleteTypeEnum;
use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Domain\Entity\Access;
use App\Module\System\Domain\Entity\RoleAccess;
use App\Module\System\Domain\Enum\RoleAccess\RoleAccessEntityRelationFieldEnum;
use App\Module\System\Domain\Interface\RoleAccess\RoleAccessWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RoleAccessWriterRepository extends ServiceEntityRepository implements RoleAccessWriterInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoleAccess::class);
    }

    public function deleteRoleAccessByRoleAndAccessInDB(Role $role, Access $access, DeleteTypeEnum $deleteTypeEnum = DeleteTypeEnum::SOFT_DELETE): void
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        if (DeleteTypeEnum::HARD_DELETE === $deleteTypeEnum) {
            $qb->delete(RoleAccess::class, RoleAccess::ALIAS)
                ->where(RoleAccess::ALIAS . '.' . RoleAccessEntityRelationFieldEnum::ROLE->value . ' = :roleUUID')
                ->andWhere(RoleAccess::ALIAS . '.' . RoleAccessEntityRelationFieldEnum::ACCESS->value . ' = :accessUUID')
                ->setParameter('roleUUID', $role->getUUID())
                ->setParameter('accessUUID', $access->getUUID())
                ->getQuery()
                ->execute();
        } else {
            $qb->update(RoleAccess::class, RoleAccess::ALIAS)
                ->set(RoleAccess::ALIAS . '.deletedAt', ':now')
                ->where(RoleAccess::ALIAS . '.' . RoleAccessEntityRelationFieldEnum::ROLE->value . ' = :roleUUID')
                ->andWhere(RoleAccess::ALIAS . '.' . RoleAccessEntityRelationFieldEnum::ACCESS->value . ' = :accessUUID')
                ->setParameter('roleUUID', $role->getUUID())
                ->setParameter('accessUUID', $access->getUUID())
                ->setParameter('now', new \DateTimeImmutable())
                ->getQuery()
                ->execute();
        }
    }
}
