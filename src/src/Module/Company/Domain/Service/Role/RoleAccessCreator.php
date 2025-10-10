<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use Doctrine\Common\Collections\Collection;

final readonly class RoleAccessCreator
{
    public function __construct(private RoleWriterInterface $roleWriterRepository)
    {
    }

    public function create(Role $role, Collection $accesses): void
    {
        foreach ($accesses as $access) {
            $role->addAccess($access);
        }

        $this->roleWriterRepository->saveRoleInDB($role);
    }
}
