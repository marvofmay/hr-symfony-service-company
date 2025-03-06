<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Employee;

use App\Module\Company\Application\Command\Employee\DeleteEmployeeCommand;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class DeleteEmployeeAction
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly EmployeeReaderInterface $employeeReaderRepository,
    ) {}

    public function execute(string $uuid): void
    {
        $this->commandBus->dispatch(new DeleteEmployeeCommand($this->employeeReaderRepository->getEmployeeByUUID($uuid)));
    }
}
