<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Department;

use App\Module\Company\Application\Command\Department\CreateDepartmentCommand;
use App\Module\Company\Domain\Service\Department\DepartmentCreator;

readonly class CreateDepartmentCommandHandler
{
    private CreateDepartmentCommand $command;

    public function __construct(private DepartmentCreator $departmentCreator,)
    {}

    public function __invoke(CreateDepartmentCommand $command): void
    {
        $this->departmentCreator->create($command);
    }
}
