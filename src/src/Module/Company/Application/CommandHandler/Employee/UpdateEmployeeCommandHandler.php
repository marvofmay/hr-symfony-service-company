<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Employee;

use App\Module\Company\Application\Command\Employee\UpdateEmployeeCommand;
use App\Module\Company\Domain\Service\Employee\EmployeeUpdater;

readonly class UpdateEmployeeCommandHandler
{
    public function __construct(private EmployeeUpdater $employeeUpdater,)
    {
    }

    public function __invoke(UpdateEmployeeCommand $command): void
    {
        $this->employeeUpdater->update($command->employee, $command);
    }
}
