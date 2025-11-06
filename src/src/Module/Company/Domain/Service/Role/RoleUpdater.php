<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleUpdaterInterface;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;

final readonly class RoleUpdater implements RoleUpdaterInterface
{
    public function __construct(private RoleWriterInterface $roleWriterRepository)
    {
    }

    public function update(Role $role, string $name, ?string $description = null): void
    {
        $role->rename($name);
        if (null !== $description) {
            $role->updateDescription($description);
        }

        $this->roleWriterRepository->saveRoleInDB($role);
    }
}
