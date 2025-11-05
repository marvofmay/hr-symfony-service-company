<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Enum\Role\RoleImportColumnEnum;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use Doctrine\Common\Collections\ArrayCollection;

readonly class RoleMultipleCreator
{
    public function __construct(private RoleWriterInterface $roleWriterRepository)
    {
    }

    public function multipleCreate(array $data): void
    {
        $roles = new ArrayCollection();
        foreach ($data as $item) {
            $role = new Role();
            $role->setName($item[RoleImportColumnEnum::ROLE_NAME->value]);
            $role->setDescription($item[RoleImportColumnEnum::ROLE_DESCRIPTION->value]);

            $roles[] = $role;
        }

        $this->roleWriterRepository->saveRolesInDB($roles);
    }
}
