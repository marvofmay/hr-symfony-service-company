<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Department;

use App\Module\Company\Application\Command\Department\DeleteMultipleDepartmentsCommand;
use App\Module\Company\Domain\Service\Department\DepartmentMultipleDeleter;

readonly class DeleteMultipleDepartmentsCommandHandler
{
    public function __construct(private DepartmentMultipleDeleter $departmentMultipleDeleter,)
    {
    }

    public function __invoke(DeleteMultipleDepartmentsCommand $command): void
    {
       $this->departmentMultipleDeleter->multipleDelete($command->departments);
    }
}
