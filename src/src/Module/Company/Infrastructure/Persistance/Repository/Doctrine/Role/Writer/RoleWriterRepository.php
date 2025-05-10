<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Role\Writer;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

class RoleWriterRepository extends ServiceEntityRepository implements RoleWriterInterface
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    public function saveRoleInDB(Role $role): void
    {
        $this->getEntityManager()->persist($role);
        $this->getEntityManager()->flush();
    }

    public function saveRolesInDB(Collection $roles): void
    {
        foreach ($roles as $role) {
            $this->getEntityManager()->persist($role);
        }
        $this->getEntityManager()->flush();
    }

    public function deleteRoleInDB(Role $role): void {
        $this->getEntityManager()->remove($role);
        $this->getEntityManager()->flush();
    }

    public function deleteMultipleRolesInDB(Collection $roles): void
    {
        if (empty($roles)) {
            return;
        }

        foreach ($roles as $role) {
            $this->getEntityManager()->remove($role);
        }

        $this->getEntityManager()->flush();        
    }
}
