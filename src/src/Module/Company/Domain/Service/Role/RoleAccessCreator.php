<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use App\Module\System\Domain\Interface\Access\AccessReaderInterface;

readonly class RoleAccessCreator
{
    public function __construct(
        private RoleWriterInterface $roleWriterRepository,
        private RoleReaderInterface $roleReaderRepository,
        private AccessReaderInterface $accessReaderRepository,
    ) {}

    public function create(string $roleUUID, array $accessUUID): void
    {
        $role = $this->roleReaderRepository->getRoleByUUID($roleUUID);
        foreach ($accessUUID as $uuid) {
            $access = $this->accessReaderRepository->getAccessByUUID($uuid);
            $role->addAccess($access);
        }

        $this->roleWriterRepository->saveRoleInDB($role);
    }
}