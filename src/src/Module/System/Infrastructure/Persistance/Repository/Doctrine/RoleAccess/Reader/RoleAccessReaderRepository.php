<?php

declare(strict_types=1);

namespace App\Module\System\Infrastructure\Persistance\Repository\Doctrine\RoleAccess\Reader;

use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Domain\Entity\Access;
use App\Module\System\Domain\Entity\RoleAccess;
use App\Module\System\Domain\Enum\RoleAccess\RoleAccessEntityRelationFieldEnum;
use App\Module\System\Domain\Interface\RoleAccess\RoleAccessReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RoleAccessReaderRepository extends ServiceEntityRepository implements RoleAccessReaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoleAccess::class);
    }

    public function isRoleHasAccess(Access $access, Role $role): bool
    {
        return null !== $this->findOneBy([
            RoleAccessEntityRelationFieldEnum::ACCESS->value => $access,
            RoleAccessEntityRelationFieldEnum::ROLE->value => $role,
        ]);
    }
}
