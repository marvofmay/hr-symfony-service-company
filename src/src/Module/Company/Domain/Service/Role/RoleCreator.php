<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleCreatorInterface;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;

readonly class RoleCreator implements RoleCreatorInterface
{
    public function __construct(private RoleWriterInterface $roleWriterRepository,)
    {
    }

    public function create(string $name, ?string $description): void
    {
        $role = new Role();
        $role->setName($name);
        $role->setDescription($description);

        $this->roleWriterRepository->saveRoleInDB($role);
    }
}
