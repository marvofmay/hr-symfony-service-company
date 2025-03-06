<?php

namespace App\Module\Company\Application\CommandHandler\Employee;

use App\Module\Company\Application\Command\Employee\DeleteEmployeeCommand;
use App\Module\Company\Domain\Service\Employee\EmployeeDeleter;

readonly class DeleteEmployeeCommandHandler
{
    public function __construct(private EmployeeDeleter $employeeDeleter,)
    {
    }

    public function __invoke(DeleteEmployeeCommand $command): void
    {
        $this->employeeDeleter->delete($command->getEmployee());
    }
}
