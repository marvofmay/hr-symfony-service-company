<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleRestorerInterface;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;

final readonly class RoleRestorer implements RoleRestorerInterface
{
    public function __construct(private RoleWriterInterface $roleWriterRepository)
    {}

    public function restore(Role $role): void
    {
        $role->setDeletedAt(null);
        $this->roleWriterRepository->saveRole($role);
    }
}
