<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Module\Company\Application\Command\Role\ImportRolesCommand;
use App\Module\Company\Domain\Service\Role\RoleMultipleCreator;

readonly class ImportRolesCommandHandler
{
    public function __construct(private RoleMultipleCreator $roleMultipleCreator,)
    {
    }

    public function __invoke(ImportRolesCommand $command): void
    {
        $this->roleMultipleCreator->multipleCreate($command->data);
    }
}
