<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Entity;

use App\Module\Company\Domain\Entity\Role;
use Doctrine\ORM\Mapping as ORM;
use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\RelationsEntityTrait;
use App\Common\Domain\Trait\TimestampableTrait;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity]
#[ORM\Table(name: 'role_access')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: false)]
class RoleAccess
{
    use TimestampableTrait;
    use AttributesEntityTrait;
    use RelationsEntityTrait;

    public const RELATION_ROLE = 'role';
    public const RELATION_ACCESS = 'access';

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(name: 'role_uuid', referencedColumnName: 'uuid', nullable: false, onDelete: 'CASCADE')]
    private Role $role;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Access::class)]
    #[ORM\JoinColumn(name: 'access_uuid', referencedColumnName: 'uuid', nullable: false, onDelete: 'CASCADE')]
    private Access $access;

    public function __construct(Role $role, Access $access,)
    {
        $this->role = $role;
        $this->access = $access;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getAccess(): Access
    {
        return $this->access;
    }
}
