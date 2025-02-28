<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Module\Company\Application\Command\Role\ImportRolesCommand;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use App\Module\Company\Domain\Service\Role\ImportRolesFromXLSX;

readonly class ImportRolesCommandHandler
{
    public function __construct(private RoleWriterInterface $roleWriterRepository,)
    {
    }

    public function __invoke(ImportRolesCommand $command): void
    {
        $roles = [];
        foreach ($command->data as $item) {
            $role = new Role();
            $role->setName($item[ImportRolesFromXLSX::COLUMN_NAME]);
            $role->setDescription($item[ImportRolesFromXLSX::COLUMN_DESCRIPTION]);

            $roles[] = $role;
        }

        $this->roleWriterRepository->saveRolesInDB($roles);
    }
}
