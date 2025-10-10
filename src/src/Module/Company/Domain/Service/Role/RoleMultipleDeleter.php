<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use Doctrine\Common\Collections\Collection;

readonly class RoleMultipleDeleter
{
    public function __construct(private RoleWriterInterface $roleWriterRepository)
    {
    }

    public function multipleDelete(Collection $roles): void
    {
        $this->roleWriterRepository->deleteMultipleRolesInDB($roles);
    }
}
