<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Role;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Domain\Entity\RoleAccess;

final readonly class RoleAssignedPermissionsEvent implements DomainEventInterface
{
    public function __construct(public Role $role,)
    {
    }

    public function getData(): array
    {
        return [
            'role' => $this->role->getUUID(),
            'accesses' => $this->role->getRoleAccesses()->map(function (RoleAccess $roleAccess) {
                return [
                    'uuid' => $roleAccess->getAccess()->getUUID(),
                    'name' => $roleAccess->getAccess()->getName(),
                    'permissions' => $roleAccess->getAccess()->getPermissions()->map(fn($permission) => [
                        'uuid' => $permission->getUUID(),
                        'name' => $permission->getName(),
                    ])->toArray(),
                ];
            })->toArray(),
        ];
    }
}