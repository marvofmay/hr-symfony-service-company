<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;

final readonly class RoleRestorer
{
    public function __construct(private RoleWriterInterface $roleWriterRepository)
    {}

    public function restore(Role $role): void
    {
        $role->setDeletedAt(null);
        $this->roleWriterRepository->saveRoleInDB($role);
    }
}
