<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Employee;

use App\Module\Company\Application\Command\Employee\DeleteMultipleEmployeesCommand;
use App\Module\Company\Domain\Service\Employee\EmployeeMultipleDeleter;

readonly class DeleteMultipleEmployeesCommandHandler
{
    public function __construct(private EmployeeMultipleDeleter $employeeMultipleDeleter,)
    {
    }

    public function __invoke(DeleteMultipleEmployeesCommand $command): void
    {
       $this->employeeMultipleDeleter->multipleDelete($command->employees);
    }
}
