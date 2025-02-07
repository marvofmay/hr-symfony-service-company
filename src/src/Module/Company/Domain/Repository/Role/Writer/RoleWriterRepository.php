<?php

declare(strict_types = 1);

namespace App\Module\Company\Domain\Repository\Role\Writer;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RoleWriterRepository extends ServiceEntityRepository implements RoleWriterInterface
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    public function saveRoleInDB(Role $role): Role
    {
        $this->getEntityManager()->persist($role);
        $this->getEntityManager()->flush();

        return $role;
    }

    public function updateRoleInDB(Role $role): Role
    {
        $this->getEntityManager()->flush();

        return $role;
    }

    public function saveRolesInDB(array $roles): void
    {
        foreach ($roles as $role) {
            $this->getEntityManager()->persist($role);
        }
        $this->getEntityManager()->flush();
    }
}