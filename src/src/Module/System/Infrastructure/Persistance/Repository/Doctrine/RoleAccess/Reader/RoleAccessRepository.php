<?php

declare(strict_types=1);

namespace App\Module\System\Infrastructure\Persistance\Repository\Doctrine\RoleAccess\Reader;

use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Domain\Entity\Access;
use App\Module\System\Domain\Entity\RoleAccessPermission;
use App\Module\System\Domain\Interface\RoleAccess\RoleAccessInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RoleAccessRepository extends ServiceEntityRepository implements RoleAccessInterface
{
    public function __construct(ManagerRegistry $registry,)
    {
        parent::__construct($registry, RoleAccessPermission::class);
    }

    public function isRoleHasAccess(Access $access, Role $role): bool
    {
        return null !== $this->findOneBy([
           RoleAccessPermission::RELATION_ACCESS => $access,
           RoleAccessPermission::RELATION_ROLE => $role,
        ]);
    }
}