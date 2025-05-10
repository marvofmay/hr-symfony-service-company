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
#[ORM\Table(name: 'role_access_permission')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: false)]
class RoleAccessPermission
{
    use TimestampableTrait;
    use AttributesEntityTrait;
    use RelationsEntityTrait;

    public const RELATION_ROLE = 'role';
    public const RELATION_ACCESS = 'access';
    public const RELATION_PERMISSION = 'permission';

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Role::class, inversedBy: 'accessPermissions')]
    #[ORM\JoinColumn(name: 'role_uuid', referencedColumnName: 'uuid', nullable: false, onDelete: 'CASCADE')]
    private Role $role;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Access::class)]
    #[ORM\JoinColumn(name: 'access_uuid', referencedColumnName: 'uuid', nullable: false, onDelete: 'CASCADE')]
    private Access $access;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Permission::class)]
    #[ORM\JoinColumn(name: 'permission_uuid', referencedColumnName: 'uuid', nullable: false, onDelete: 'CASCADE')]
    private Permission $permission;

    public function __construct(Role $role, Access $access, Permission $permission)
    {
        $this->role = $role;
        $this->access = $access;
        $this->permission = $permission;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getAccess(): Access
    {
        return $this->access;
    }

    public function getPermission(): Permission
    {
        return $this->permission;
    }
}
