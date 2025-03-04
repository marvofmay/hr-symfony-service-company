<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Department;

use App\Module\Company\Application\Command\Department\UpdateDepartmentCommand;
use App\Module\Company\Domain\Service\Department\DepartmentUpdater;

readonly class UpdateDepartmentCommandHandler
{
    public function __construct(private DepartmentUpdater $departmentUpdater,)
    {
    }

    public function __invoke(UpdateDepartmentCommand $command): void
    {
        $this->departmentUpdater->update($command);
    }
}
