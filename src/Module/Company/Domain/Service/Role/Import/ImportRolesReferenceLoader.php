<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role\Import;

use App\Module\Company\Domain\Enum\Role\RoleImportColumnEnum;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;

final class ImportRolesReferenceLoader
{
    public array $roles = [] {
        get {
            return $this->roles;
        }
    }

    public function __construct(private readonly RoleReaderInterface $roleReaderRepository)
    {
    }

    public function preload(array $rows): void
    {
        $roleNames = [];

        foreach ($rows as $row) {
            if (!empty($row[RoleImportColumnEnum::ROLE_NAME->value])) {
                $roleNames[] = trim((string) $row[RoleImportColumnEnum::ROLE_NAME->value]);
            }
        }

        $roleNames = array_unique($roleNames);

        $this->roles = $this->mapByName($this->roleReaderRepository->getRolesByNames($roleNames));
    }

    public function mapByName(iterable $roles): array
    {
        $map = [];
        foreach ($roles as $role) {
            $map[trim($role->getName())] = $role;
        }

        return $map;
    }
}
