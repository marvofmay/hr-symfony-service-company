<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role\Import;

use App\Module\Company\Domain\Enum\Role\RoleImportColumnEnum;
use App\Module\Company\Domain\Interface\Role\Import\RolesImporterInterface;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use Doctrine\Common\Collections\ArrayCollection;

final class RolesImporter implements RolesImporterInterface
{
    private array $roles = [];

    public function __construct(
        private readonly RoleWriterInterface $roleWriterRepository,
        private readonly RoleFactory $roleFactory,
    ) {
    }

    public function save(array $preparedRows, array $existingRoles): void
    {
        foreach ($preparedRows as $preparedRow) {
            $roleName = $preparedRow[RoleImportColumnEnum::ROLE_NAME->value];
            if (array_key_exists($roleName, $existingRoles)) {
                $role = $this->roleFactory->update(role: $existingRoles[$roleName], roleData: $preparedRow);
            } else {
                $role = $this->roleFactory->create(roleData: $preparedRow);
            }

            $this->roles[] = $role;
        }

        $this->roleWriterRepository->saveRoles(new ArrayCollection($this->roles));
    }
}
