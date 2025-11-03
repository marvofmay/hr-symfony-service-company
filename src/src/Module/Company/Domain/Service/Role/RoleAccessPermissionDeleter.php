<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Common\Domain\Enum\DeleteTypeEnum;
use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Domain\Entity\Access;
use App\Module\System\Domain\Interface\RoleAccessPermission\RoleAccessPermissionWriterInterface;

final readonly class RoleAccessPermissionDeleter
{
    public function __construct(private RoleAccessPermissionWriterInterface $roleAccessPermissionWriterRepository,)
    {
    }

    public function delete(Role $role, Access $access): void
    {
        $this->roleAccessPermissionWriterRepository->deleteRoleAccessPermissionsByRoleAndAccessInDB($role, $access, DeleteTypeEnum::HARD_DELETE);
    }
}
