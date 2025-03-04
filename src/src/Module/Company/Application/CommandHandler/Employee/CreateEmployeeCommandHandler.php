<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Employee;

use App\Module\Company\Application\Command\Employee\CreateEmployeeCommand;
use App\Module\Company\Domain\Service\Employee\EmployeeCreator;

readonly class CreateEmployeeCommandHandler
{
    public function __construct(private EmployeeCreator $employeeCreator,)
    {
    }

    public function __invoke(CreateEmployeeCommand $command): void
    {
        $this->employeeCreator->create($command);
    }
}
