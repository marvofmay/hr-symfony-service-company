<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Role\Writer;

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

    public function saveRoleInDB(Role $role): void
    {
        $this->getEntityManager()->persist($role);
        $this->getEntityManager()->flush();
    }

    public function updateRoleInDB(Role $role): void
    {
        $this->getEntityManager()->flush();
    }

    public function saveRolesInDB(array $roles): void
    {
        foreach ($roles as $role) {
            $this->getEntityManager()->persist($role);
        }
        $this->getEntityManager()->flush();
    }

    public function deleteMultipleRolesInDB(array $selectedUUID): void
    {
        if (empty($selectedUUID)) {
            return;
        }

        $query = $this->getEntityManager()->createQuery('UPDATE App\Module\Company\Domain\Entity\Role r SET r.deletedAt = :deletedAt WHERE r.uuid IN (:uuids)');
        $query->setParameter('deletedAt', (new \DateTime())->format('Y-m-d H:i:s'));
        $query->setParameter('uuids', $selectedUUID);

        $query->execute();
    }
}
