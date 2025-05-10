<?php

declare(strict_types=1);

namespace App\Module\System\Infrastructure\Persistance\Repository\Doctrine\RoleAccessPermission\Reader;

use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Domain\Entity\Access;
use App\Module\System\Domain\Entity\Permission;
use App\Module\System\Domain\Entity\RoleAccessPermission;
use App\Module\System\Domain\Interface\RoleAccessPermission\RoleAccessPermissionInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RoleAccessPermissionRepository extends ServiceEntityRepository implements RoleAccessPermissionInterface
{
    public function __construct(ManagerRegistry $registry,)
    {
        parent::__construct($registry, RoleAccessPermission::class);
    }

    public function isRoleHasAccessAndPermission(Permission $permission, Access $access, Role $role): bool
    {
        return null !== $this->findOneBy([
           RoleAccessPermission::RELATION_PERMISSION => $permission,
           RoleAccessPermission::RELATION_ACCESS => $access,
           RoleAccessPermission::RELATION_ROLE => $role,
        ]);
    }
}