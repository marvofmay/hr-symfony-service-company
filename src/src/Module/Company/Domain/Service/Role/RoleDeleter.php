<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleDeleterInterface;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;

final readonly class RoleDeleter implements RoleDeleterInterface
{
    public function __construct(private RoleWriterInterface $roleWriterRepository)
    {
    }

    public function delete(Role $role): void
    {
        $this->roleWriterRepository->deleteRoleInDB($role);
    }
}
