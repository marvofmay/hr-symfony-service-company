<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Common\Domain\Enum\DeleteTypeEnum;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleAccessDeleterInterface;
use App\Module\System\Domain\Interface\RoleAccess\RoleAccessWriterInterface;

final readonly class RoleAccessDeleter implements RoleAccessDeleterInterface
{
    public function __construct(private RoleAccessWriterInterface $roleAccessWriterRepository)
    {
    }

    public function delete(Role $role): void
    {
        $this->roleAccessWriterRepository->deleteRoleAccessesByRoleInDB(role: $role, deleteTypeEnum: DeleteTypeEnum::HARD_DELETE);
    }
}
